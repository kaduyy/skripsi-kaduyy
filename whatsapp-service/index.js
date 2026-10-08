const express = require('express');
const {
    makeWASocket,
    useMultiFileAuthState,
    DisconnectReason,
    Browsers,
    fetchLatestBaileysVersion,
    makeCacheableSignalKeyStore
} = require('@whiskeysockets/baileys');
const pino = require('pino');
const QRCode = require('qrcode');
const cors = require('cors');
const path = require('path');
const fs = require('fs');

// ============================================================
//  CONFIG
// ============================================================
const PORT = process.env.WA_PORT || 3000;
const AUTH_DIR = path.join(__dirname, 'auth_info_baileys');

// ============================================================
//  STATE
// ============================================================
/** @type {'disconnected'|'connecting'|'qr_pending'|'connected'} */
let connectionState = 'disconnected';
let sock = null;
let currentQR = null;
let qrTimestamp = 0;
let reconnectAttempts = 0;
let reconnectTimer = null;
let isConnecting = false;
let isPairingActive = false;
let hasPrintedTerminalNotice = false;

// ============================================================
//  LOGGER HELPER
// ============================================================
function log(level, ...args) {
    const ts = new Date().toLocaleTimeString('id-ID', { hour12: false });
    const prefix = { INFO: '✅', WARN: '⚠️', ERROR: '❌', SYSTEM: '🔄' };
    console.log(`[${ts}] ${prefix[level] || 'ℹ️'} [${level}]`, ...args);
}

// Suppress noisy internal Baileys logs
const originalLog = console.log;
console.log = function (...args) {
    if (typeof args[0] === 'string') {
        const text = args[0];
        if (
            text.includes('Closing session:') ||
            text.includes('Stream Errored') ||
            text.includes('connection closed due to') ||
            text.includes('pre-key')
        ) {
            return;
        }
    }
    originalLog.apply(console, args);
};

// ============================================================
//  CLEAN AUTH DIRECTORY HELPER
// ============================================================
function clearAuthFolder() {
    try {
        if (fs.existsSync(AUTH_DIR)) {
            fs.rmSync(AUTH_DIR, { recursive: true, force: true });
        }
        fs.mkdirSync(AUTH_DIR, { recursive: true });
        log('SYSTEM', 'Folder sesi auth_info_baileys telah dibersihkan.');
    } catch (err) {
        log('ERROR', 'Gagal membersihkan auth_info_baileys:', err.message);
    }
}

// ============================================================
//  PHONE NUMBER NORMALIZATION
// ============================================================
function normalizePhone(phone) {
    if (!phone) return null;

    let cleaned = phone.replace(/[^0-9]/g, '');

    if (cleaned.startsWith('0')) {
        cleaned = '62' + cleaned.substring(1);
    }
    if (!cleaned.startsWith('62')) {
        cleaned = '62' + cleaned;
    }

    if (cleaned.length < 10 || cleaned.length > 15) {
        return null;
    }

    return cleaned;
}

// ============================================================
//  WHATSAPP CONNECTION
// ============================================================
async function connectToWhatsApp() {
    if (isConnecting) {
        return;
    }
    isConnecting = true;

    // Destroy previous socket cleanly
    if (sock) {
        try {
            sock.ev.removeAllListeners('connection.update');
            sock.ev.removeAllListeners('creds.update');
            sock.end(undefined);
        } catch (_) {}
        sock = null;
    }

    connectionState = 'connecting';
    currentQR = null;

    try {
        if (!fs.existsSync(AUTH_DIR)) {
            fs.mkdirSync(AUTH_DIR, { recursive: true });
        }

        const logger = pino({ level: 'silent' });
        const { state, saveCreds } = await useMultiFileAuthState(AUTH_DIR);
        const { version } = await fetchLatestBaileysVersion();

        sock = makeWASocket({
            version,
            logger,
            printQRInTerminal: false,
            auth: {
                creds: state.creds,
                keys: makeCacheableSignalKeyStore(state.keys, logger),
            },
            browser: Browsers.ubuntu('Chrome'),
            connectTimeoutMs: 60000,
            defaultQueryTimeoutMs: 60000,
            keepAliveIntervalMs: 25000,
            markOnlineOnConnect: false,
            generateHighQualityLinkPreview: false,
            syncFullHistory: false,
            getMessage: async () => undefined
        });

        sock.ev.on('connection.update', (update) => {
            const { connection, lastDisconnect, qr } = update;

            if (qr) {
                currentQR = qr;
                qrTimestamp = Date.now();
                connectionState = 'qr_pending';
                isConnecting = false;

                if (!hasPrintedTerminalNotice) {
                    hasPrintedTerminalNotice = true;
                    log('INFO', '════════════════════════════════════════════════════════════════');
                    log('INFO', `🌐 Buka link ini di browser untuk scan QR atau pairing code:`);
                    log('INFO', `👉 http://localhost:${PORT}/scan`);
                    log('INFO', `👉 Atau buka Admin Dashboard: http://localhost:8000/admin/dashboard`);
                    log('INFO', '════════════════════════════════════════════════════════════════');
                }
            }

            if (connection === 'close') {
                const statusCode = lastDisconnect?.error?.output?.statusCode;
                connectionState = 'disconnected';
                currentQR = null;
                isConnecting = false;

                // Terminal disconnect reasons (session expired/unlinked/replaced)
                const isSessionInvalid = [
                    DisconnectReason.loggedOut,           // 401
                    DisconnectReason.forbidden,           // 403
                    DisconnectReason.multideviceMismatch, // 411
                    DisconnectReason.connectionReplaced,  // 440
                    DisconnectReason.badSession,          // 500
                    401, 403, 411, 440, 500
                ].includes(statusCode);

                if (isSessionInvalid) {
                    log('WARN', `Sesi WhatsApp tidak valid (Kode: ${statusCode}). Mereset sesi...`);
                    clearAuthFolder();
                    reconnectAttempts = 0;
                    hasPrintedTerminalNotice = false;
                    isPairingActive = false;
                    scheduleReconnect(1500);
                    return;
                }

                if (statusCode === 515 || statusCode === DisconnectReason.restartRequired) {
                    log('SYSTEM', 'Menyinkronkan sesi enkripsi dengan server WhatsApp...');
                    scheduleReconnect(1000);
                    return;
                }

                // If user is currently pairing on phone, don't interrupt immediately
                if (statusCode === 408 || statusCode === DisconnectReason.timedOut) {
                    log('SYSTEM', 'Koneksi standby diperbarui (Kode: 408 Timeout). Menghubungkan ulang...');
                    scheduleReconnect(2000);
                    return;
                }

                log('WARN', `Koneksi terputus (Kode: ${statusCode || 'Unknown'}).`);
                scheduleReconnect();

            } else if (connection === 'open') {
                connectionState = 'connected';
                currentQR = null;
                reconnectAttempts = 0;
                isConnecting = false;
                isPairingActive = false;
                hasPrintedTerminalNotice = false;
                log('INFO', '🎉 SISTEM NOTIFIKASI WHATSAPP BERHASIL TERHUBUNG & SIAP DIGUNAKAN!');
            }
        });

        sock.ev.on('creds.update', saveCreds);

    } catch (err) {
        log('ERROR', 'Gagal menginisialisasi koneksi:', err.message);
        connectionState = 'disconnected';
        isConnecting = false;
        scheduleReconnect();
    }
}

// ============================================================
//  RECONNECT HELPER
// ============================================================
function scheduleReconnect(customDelay = null) {
    if (reconnectTimer) {
        clearTimeout(reconnectTimer);
    }

    const delay = customDelay !== null ? customDelay : Math.min(2000 * Math.pow(2, reconnectAttempts), 30000);
    if (customDelay === null) {
        reconnectAttempts++;
    }

    reconnectTimer = setTimeout(() => {
        connectToWhatsApp();
    }, delay);
}

// ============================================================
//  EXPRESS SERVER
// ============================================================
const app = express();
app.use(cors());
app.use(express.json());

// --- STATUS ENDPOINT ---
app.get('/status', (req, res) => {
    const statusInfo = {
        status: connectionState,
        connected: connectionState === 'connected',
        qr_available: connectionState === 'qr_pending' && currentQR !== null,
        qr_age_seconds: currentQR ? Math.floor((Date.now() - qrTimestamp) / 1000) : 0,
        reconnect_attempts: reconnectAttempts,
        uptime: process.uptime(),
    };

    if (connectionState === 'connected' && sock?.user) {
        const rawJid = sock.user.id || '';
        const cleanNumber = rawJid.split('@')[0].split(':')[0];
        const formattedPhone = cleanNumber.startsWith('62') ? '0' + cleanNumber.substring(2) : cleanNumber;
        
        statusInfo.user = {
            id: sock.user.id,
            phone: formattedPhone,
            name: sock.user.name || 'Alan Salon',
        };
        statusInfo.account = formattedPhone;
    }

    res.json(statusInfo);
});

// --- QR CODE JSON ENDPOINT ---
app.get('/qr', async (req, res) => {
    if (connectionState === 'connected') {
        return res.json({
            status: 'already_connected',
            message: 'WhatsApp sudah terhubung, tidak perlu scan QR.',
        });
    }

    if (!currentQR) {
        return res.json({
            status: 'no_qr',
            message: 'QR Code sedang disiapkan... Mohon tunggu 2-3 detik.',
        });
    }

    try {
        const qrDataUri = await QRCode.toDataURL(currentQR, {
            width: 320,
            margin: 2,
            color: { dark: '#1A3B32', light: '#FFFFFF' },
            errorCorrectionLevel: 'M'
        });

        res.json({
            status: 'qr_ready',
            qr: qrDataUri,
            age_seconds: Math.floor((Date.now() - qrTimestamp) / 1000),
            message: 'Scan QR Code di bawah dengan WhatsApp Anda.',
        });
    } catch (err) {
        res.status(500).json({ status: 'error', message: 'Gagal generate QR: ' + err.message });
    }
});

// --- PAIRING CODE ENDPOINT ---
app.post('/pairing-code', async (req, res) => {
    try {
        const { phone } = req.body;
        if (!phone) {
            return res.status(400).json({ success: false, error: 'Nomor WhatsApp wajib diisi' });
        }

        if (connectionState === 'connected') {
            return res.json({ success: false, message: 'WhatsApp sudah terhubung!' });
        }

        const cleanPhone = normalizePhone(phone);
        if (!cleanPhone) {
            return res.status(400).json({ success: false, error: 'Format nomor WhatsApp tidak valid. Masukkan nomor seperti 08123456789 atau 628123456789' });
        }

        // If socket is not yet ready or disconnected, wait or connect
        if (!sock) {
            await connectToWhatsApp();
            await new Promise(r => setTimeout(r, 2000));
        }

        isPairingActive = true;
        log('INFO', `Membuat 8-digit Pairing Code untuk nomor: ${cleanPhone}...`);

        const rawCode = await sock.requestPairingCode(cleanPhone);
        const formattedCode = rawCode?.match(/.{1,4}/g)?.join(' - ') || rawCode;

        log('INFO', `Pairing Code siap: [ ${formattedCode} ] (Raw: ${rawCode})`);

        return res.json({
            success: true,
            code: formattedCode,
            raw_code: rawCode,
            phone: cleanPhone,
            message: 'Buka WhatsApp di HP > Perangkat Tertaut > Tautkan dengan nomor telepon > Masukkan 8 karakter ini.'
        });
    } catch (error) {
        log('ERROR', 'Gagal membuat pairing code:', error.message);
        isPairingActive = false;
        return res.status(500).json({ success: false, error: error.message });
    }
});

// --- DEDICATED WEB SCAN & PAIRING UI PAGE ---
app.get(['/', '/scan', '/qr-view'], (req, res) => {
    res.send(`
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hubungkan WhatsApp - Alan's Salon</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-[#FAFAF9] text-gray-800 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white max-w-md w-full rounded-3xl shadow-xl border border-gray-100 p-8 text-center">
        <!-- Logo & Header -->
        <div class="flex items-center justify-center gap-2 mb-2">
            <div class="w-10 h-10 bg-[#1A3B32] text-white rounded-2xl flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 text-[#E76F51]">
                    <path fill-rule="evenodd" d="M3 6a3 3 0 0 1 3-3h12a3 3 0 0 1 3 3v12a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V6Zm14.25 6a1.5 1.5 0 0 1-1.5 1.5H8.25a1.5 1.5 0 0 1-1.5-1.5V9.75a1.5 1.5 0 0 1 1.5-1.5h9a1.5 1.5 0 0 1 1.5 1.5V12Z" clip-rule="evenodd" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Alan's Salon</h1>
        </div>
        <p class="text-sm text-gray-500 mb-6">Hubungkan WhatsApp Admin untuk Notifikasi Reservasi Otomatis</p>

        <!-- Status Indicator -->
        <div id="status-bar" class="mb-6 p-3 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center gap-2">
            <span id="status-dot" class="w-3 h-3 rounded-full bg-yellow-400 animate-pulse"></span>
            <span id="status-text" class="text-sm font-medium text-gray-700">Memeriksa koneksi...</span>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex bg-gray-100 p-1 rounded-2xl mb-6">
            <button id="tab-code-btn" onclick="switchTab('code')" class="flex-1 py-2 rounded-xl text-xs font-semibold bg-white text-[#1A3B32] shadow-sm transition">
                🔢 Kode Pairing (8-Digit)
            </button>
            <button id="tab-qr-btn" onclick="switchTab('qr')" class="flex-1 py-2 rounded-xl text-xs font-semibold text-gray-500 hover:text-gray-900 transition">
                📷 Scan QR Code
            </button>
        </div>

        <!-- TAB 1: PAIRING CODE -->
        <div id="tab-code" class="space-y-4 text-left">
            <div class="bg-gray-50 p-5 rounded-2xl border border-gray-100 space-y-3">
                <label class="block text-xs font-semibold text-gray-700">Nomor WhatsApp HP Anda:</label>
                <div class="flex gap-2">
                    <input id="pairing-phone" type="text" placeholder="Contoh: 08123456789" class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#1A3B32]">
                    <button onclick="requestPairingCode()" id="btn-get-code" class="bg-[#1A3B32] hover:bg-[#132c25] text-white px-4 py-2.5 rounded-xl text-xs font-semibold transition">
                        Dapatkan Kode
                    </button>
                </div>
                <p class="text-[11px] text-gray-400">Nomor akun WhatsApp yang sedang aktif di HP.</p>
            </div>

            <!-- Pairing Code Result Display -->
            <div id="code-result" class="hidden p-5 rounded-2xl bg-emerald-50 border border-emerald-200 text-center space-y-3">
                <span class="text-xs text-emerald-700 font-semibold uppercase tracking-wider">KODE PAIRING ANDA</span>
                <div id="display-code" class="text-3xl font-mono font-bold tracking-widest text-[#1A3B32] py-2 bg-white rounded-xl border border-emerald-300 select-all cursor-pointer">
                    ----
                </div>
                <div class="bg-white/90 p-3 rounded-xl text-xs text-left text-gray-600 space-y-1 border border-emerald-100">
                    <p class="font-bold text-gray-800">📱 Langkah di WhatsApp HP:</p>
                    <p>1. Buka <b>WhatsApp</b> di HP</p>
                    <p>2. Buka <b>Perangkat Tertaut (Linked Devices)</b></p>
                    <p>3. Ketuk <b>Tautkan dengan nomor telepon saja</b></p>
                    <p>4. Ketik 8 karakter di atas (<b>tanpa spasi / tanda strip</b>).</p>
                </div>
            </div>
        </div>

        <!-- TAB 2: QR CODE -->
        <div id="tab-qr" class="hidden space-y-4">
            <div class="relative bg-gray-50 p-4 rounded-2xl border border-gray-100 flex items-center justify-center min-h-[300px]">
                <div id="qr-loading" class="flex flex-col items-center gap-3">
                    <div class="animate-spin w-8 h-8 border-4 border-[#1A3B32] border-t-transparent rounded-full"></div>
                    <span class="text-xs text-gray-500">Menyiapkan QR Code jernih...</span>
                </div>
                <img id="qr-image" src="" alt="WhatsApp QR Code" class="hidden w-64 h-64 rounded-xl shadow-sm border border-gray-200">
            </div>

            <div id="timer-box" class="hidden text-xs text-gray-500">
                <span>Auto refresh dalam: </span>
                <span id="countdown" class="font-bold text-[#E76F51]">20s</span>
            </div>

            <div class="text-left bg-emerald-50/60 p-4 rounded-2xl border border-emerald-100 text-xs text-emerald-900 space-y-1">
                <p class="font-semibold text-emerald-800">Cara Scan:</p>
                <ol class="list-decimal list-inside space-y-1 text-emerald-700">
                    <li>Buka WhatsApp di HP Anda</li>
                    <li>Pilih <b>Perangkat Tertaut</b> > <b>Tautkan Perangkat</b></li>
                    <li>Arahkan kamera ke QR Code di atas</li>
                </ol>
            </div>
        </div>

        <!-- Action Footer -->
        <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between text-xs text-gray-400">
            <button onclick="resetSession()" class="text-red-500 hover:underline">
                🔄 Reset Sesi
            </button>
            <a href="http://localhost:8000/admin/dashboard" class="text-[#1A3B32] font-semibold hover:underline">
                Kembali ke Dashboard →
            </a>
        </div>
    </div>

    <script>
        let currentTab = 'code';
        let countdownSecs = 20;
        let lastQR = '';

        function switchTab(tab) {
            currentTab = tab;
            const tabQr = document.getElementById('tab-qr');
            const tabCode = document.getElementById('tab-code');
            const btnQr = document.getElementById('tab-qr-btn');
            const btnCode = document.getElementById('tab-code-btn');

            if (tab === 'qr') {
                tabQr.classList.remove('hidden');
                tabCode.classList.add('hidden');
                btnQr.className = 'flex-1 py-2 rounded-xl text-xs font-semibold bg-white text-[#1A3B32] shadow-sm transition';
                btnCode.className = 'flex-1 py-2 rounded-xl text-xs font-semibold text-gray-500 hover:text-gray-900 transition';
                fetchQR();
            } else {
                tabQr.classList.add('hidden');
                tabCode.classList.remove('hidden');
                btnCode.className = 'flex-1 py-2 rounded-xl text-xs font-semibold bg-white text-[#1A3B32] shadow-sm transition';
                btnQr.className = 'flex-1 py-2 rounded-xl text-xs font-semibold text-gray-500 hover:text-gray-900 transition';
            }
        }

        async function fetchQR() {
            try {
                const res = await fetch('/qr');
                const data = await res.json();

                const qrImg = document.getElementById('qr-image');
                const qrLoading = document.getElementById('qr-loading');
                const timerBox = document.getElementById('timer-box');

                if (data.status === 'already_connected') {
                    setConnectedStatus();
                    return;
                }

                if (data.status === 'qr_ready' && data.qr) {
                    if (data.qr !== lastQR) {
                        lastQR = data.qr;
                        qrImg.src = data.qr;
                        qrImg.classList.remove('hidden');
                        qrLoading.classList.add('hidden');
                        timerBox.classList.remove('hidden');
                        countdownSecs = 20;
                    }
                }
            } catch (err) {
                console.error(err);
            }
        }

        async function checkStatus() {
            try {
                const res = await fetch('/status');
                const data = await res.json();

                const dot = document.getElementById('status-dot');
                const text = document.getElementById('status-text');

                if (data.connected) {
                    setConnectedStatus(data.user?.name);
                } else if (data.status === 'qr_pending') {
                    dot.className = 'w-3 h-3 rounded-full bg-yellow-400 animate-pulse';
                    text.textContent = 'Menunggu Hubungkan WhatsApp';
                    text.className = 'text-sm font-medium text-yellow-800';
                    if (currentTab === 'qr') {
                        fetchQR();
                    }
                } else if (data.status === 'connecting') {
                    dot.className = 'w-3 h-3 rounded-full bg-blue-400 animate-pulse';
                    text.textContent = 'Sedang menghubungkan ke server WhatsApp...';
                    text.className = 'text-sm font-medium text-blue-800';
                } else {
                    dot.className = 'w-3 h-3 rounded-full bg-gray-400';
                    text.textContent = 'Terputus';
                    text.className = 'text-sm font-medium text-gray-600';
                }
            } catch (err) {
                document.getElementById('status-dot').className = 'w-3 h-3 rounded-full bg-red-400';
                document.getElementById('status-text').textContent = 'Service WhatsApp Offline';
            }
        }

        function setConnectedStatus(name) {
            const dot = document.getElementById('status-dot');
            const text = document.getElementById('status-text');
            dot.className = 'w-3 h-3 rounded-full bg-green-500';
            text.textContent = '✅ WhatsApp Terhubung ' + (name ? '(' + name + ')' : '');
            text.className = 'text-sm font-bold text-green-700';

            document.getElementById('tab-qr').innerHTML = '<div class="p-8 text-center"><div class="text-5xl mb-3">🎉</div><h3 class="font-bold text-lg text-gray-900">WhatsApp Sudah Aktif!</h3><p class="text-xs text-gray-500 mt-1">Sistem notifikasi reservasi siap mengirim pesan ke pelanggan.</p></div>';
            document.getElementById('tab-code').innerHTML = '<div class="p-8 text-center"><div class="text-5xl mb-3">🎉</div><h3 class="font-bold text-lg text-gray-900">WhatsApp Sudah Aktif!</h3></div>';
        }

        async function requestPairingCode() {
            const phone = document.getElementById('pairing-phone').value.trim();
            if (!phone) {
                alert('Silakan masukkan nomor WhatsApp Anda.');
                return;
            }

            const btn = document.getElementById('btn-get-code');
            btn.disabled = true;
            btn.textContent = 'Memproses...';

            try {
                const res = await fetch('/pairing-code', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ phone })
                });
                const data = await res.json();

                if (data.success && data.code) {
                    document.getElementById('code-result').classList.remove('hidden');
                    document.getElementById('display-code').textContent = data.code;
                } else {
                    alert(data.error || data.message || 'Gagal membuat kode pairing');
                }
            } catch (err) {
                alert('Gagal menghubungi service: ' + err.message);
            } finally {
                btn.disabled = false;
                btn.textContent = 'Dapatkan Kode';
            }
        }

        async function resetSession() {
            if (!confirm('Apakah Anda yakin ingin mereset sesi WhatsApp?')) return;
            await fetch('/reset-session', { method: 'POST' });
            alert('Sesi berhasil direset. Silakan tunggu 2 detik.');
            location.reload();
        }

        setInterval(() => {
            if (countdownSecs > 0) {
                countdownSecs--;
                const el = document.getElementById('countdown');
                if (el) el.textContent = countdownSecs + 's';
            }
        }, 1000);

        setInterval(checkStatus, 3000);
        checkStatus();
    </script>
</body>
</html>
    `);
});

// --- SEND MESSAGE ENDPOINT ---
app.post('/send-message', async (req, res) => {
    try {
        const { phone, message } = req.body;

        if (!phone || !message) {
            return res.status(400).json({
                success: false,
                error: 'Phone and message are required',
            });
        }

        if (connectionState !== 'connected' || !sock) {
            return res.status(503).json({
                success: false,
                error: 'WhatsApp belum terhubung. Status: ' + connectionState,
            });
        }

        const normalizedPhone = normalizePhone(phone);
        if (!normalizedPhone) {
            return res.status(400).json({
                success: false,
                error: 'Format nomor telepon tidak valid: ' + phone,
            });
        }

        const jid = normalizedPhone + '@s.whatsapp.net';

        const sendPromise = sock.sendMessage(jid, { text: message });
        const timeoutPromise = new Promise((_, reject) =>
            setTimeout(() => reject(new Error('Timeout: pengiriman pesan melebihi 30 detik')), 30000)
        );

        await Promise.race([sendPromise, timeoutPromise]);

        log('INFO', `Pesan terkirim ke ${normalizedPhone}`);

        return res.status(200).json({
            success: true,
            message: 'Pesan berhasil terkirim',
            phone: normalizedPhone,
        });
    } catch (error) {
        log('ERROR', 'Gagal mengirim pesan:', error.message);
        return res.status(500).json({
            success: false,
            error: error.message,
        });
    }
});

// --- RESET SESSION ENDPOINT ---
app.post('/reset-session', async (req, res) => {
    try {
        log('SYSTEM', 'Reset sesi diminta oleh pengguna / admin...');

        if (sock) {
            try {
                await sock.logout();
            } catch (_) {}
            sock = null;
        }

        clearAuthFolder();

        connectionState = 'disconnected';
        currentQR = null;
        reconnectAttempts = 0;
        isConnecting = false;
        isPairingActive = false;
        hasPrintedTerminalNotice = false;

        setTimeout(() => connectToWhatsApp(), 1000);

        res.json({
            success: true,
            message: 'Sesi telah direset. QR Code baru akan tersedia dalam beberapa detik.',
        });
    } catch (error) {
        log('ERROR', 'Gagal reset sesi:', error.message);
        res.status(500).json({ success: false, error: error.message });
    }
});

// --- JSON PARSE ERROR HANDLER ---
app.use((err, req, res, next) => {
    if (err instanceof SyntaxError && err.status === 400 && 'body' in err) {
        return res.status(400).json({ error: 'Invalid JSON payload format' });
    }
    next(err);
});

// ============================================================
//  GRACEFUL SHUTDOWN
// ============================================================
async function gracefulShutdown(signal) {
    log('SYSTEM', `${signal} diterima. Menutup koneksi WhatsApp...`);

    if (reconnectTimer) {
        clearTimeout(reconnectTimer);
    }

    if (sock) {
        try {
            sock.ev.removeAllListeners('connection.update');
            sock.ev.removeAllListeners('creds.update');
            sock.end(undefined);
        } catch (_) {}
    }

    log('SYSTEM', 'WhatsApp Service ditutup dengan bersih.');
    process.exit(0);
}

process.on('SIGINT', () => gracefulShutdown('SIGINT'));
process.on('SIGTERM', () => gracefulShutdown('SIGTERM'));

process.on('uncaughtException', (err) => {
    log('ERROR', 'Uncaught Exception:', err.message);
});

process.on('unhandledRejection', (reason) => {
    log('ERROR', 'Unhandled Rejection:', reason?.message || reason);
});

// ============================================================
//  START SERVER
// ============================================================
app.listen(PORT, () => {
    log('INFO', `WhatsApp Service berjalan di http://localhost:${PORT}`);
    log('INFO', `🔗 Buka di browser: http://localhost:${PORT}/scan untuk menghubungkan WhatsApp`);
    connectToWhatsApp();
});
