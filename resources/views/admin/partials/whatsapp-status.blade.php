{{-- WhatsApp Connection Status Widget --}}
<div id="wa-status-widget" class="flex-shrink-0 px-4 pb-4">
    <div class="bg-white/10 rounded-2xl p-3.5 backdrop-blur-sm border border-white/10">
        <div class="flex items-center justify-between mb-2.5">
            <div class="flex items-center gap-2">
                <!-- Official WhatsApp Brand Icon -->
                <i class="fa-brands fa-whatsapp text-emerald-400 text-base"></i>
                <span class="text-xs font-semibold text-white/90 uppercase tracking-wider">WhatsApp API</span>
            </div>
            <a href="http://localhost:3000/scan" target="_blank" title="Buka Halaman Scan di Tab Baru" class="text-white/60 hover:text-white transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
                </svg>
            </a>
        </div>

        {{-- Status Indicator --}}
        <div id="wa-status-indicator" class="flex items-center gap-2">
            <span id="wa-status-dot" class="w-2.5 h-2.5 rounded-full bg-gray-400 animate-pulse"></span>
            <span id="wa-status-text" class="text-xs text-white/70">Memeriksa...</span>
        </div>

        {{-- Hubungkan Button --}}
        <button id="wa-qr-btn" onclick="showWaModal()" class="hidden mt-2.5 w-full text-xs bg-white/20 hover:bg-white/30 text-white py-2 px-3 rounded-xl transition font-semibold flex items-center justify-center gap-2">
            <i class="fa-brands fa-whatsapp text-emerald-400 text-sm"></i>
            <span>Hubungkan WhatsApp</span>
        </button>
    </div>
</div>

{{-- WhatsApp Connection Modal (QR & Pairing Code) --}}
<div id="wa-qr-modal"
    class="fixed inset-0 z-[99999] hidden items-center justify-center bg-white"
    style="display:none;">
    <div class="bg-white rounded-3xl shadow-2xl p-6 max-w-md w-full mx-4 text-center border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2.5 text-left">
                <div class="w-10 h-10 rounded-xl bg-[#25D366] text-white flex items-center justify-center shadow-xs text-xl">
                    <i class="fa-brands fa-whatsapp text-white"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900 leading-tight">Hubungkan WhatsApp</h3>
                    <p class="text-xs text-gray-400">Pilih scan QR Code atau Kode Pairing 8 digit</p>
                </div>
            </div>
            <button onclick="closeWaModal()" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg transition hover:bg-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Switcher Tabs --}}
        <div class="flex bg-gray-100 p-1 rounded-xl mb-4">
            <button id="modal-tab-qr-btn" onclick="switchModalTab('qr')" class="flex-1 py-1.5 rounded-lg text-xs font-semibold bg-white text-[#1A3B32] shadow-sm transition flex items-center justify-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-3.5 h-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                </svg>
                <span>Scan QR Code</span>
            </button>
            <button id="modal-tab-code-btn" onclick="switchModalTab('code')" class="flex-1 py-1.5 rounded-lg text-xs font-semibold text-gray-500 hover:text-gray-900 transition flex items-center justify-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-3.5 h-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                </svg>
                <span>Kode Pairing</span>
            </button>
        </div>

        {{-- TAB 1: QR Code --}}
        <div id="modal-tab-qr" class="space-y-3">
            <div id="wa-qr-container" class="bg-gray-50 p-4 rounded-2xl border border-gray-100 flex flex-col items-center justify-center min-h-[260px]">
                <div class="animate-spin w-8 h-8 border-4 border-[#1A3B32] border-t-transparent rounded-full mb-2"></div>
                <span class="text-xs text-gray-500">Menyiapkan QR Code...</span>
            </div>
            <div id="modal-qr-timer" class="hidden text-xs text-gray-400 flex items-center justify-center gap-1">
                <span>Auto refresh dalam:</span>
                <span id="modal-countdown" class="font-bold text-[#E76F51]">20s</span>
            </div>
            <p class="text-xs text-gray-500 text-left bg-gray-50 p-3 rounded-xl">
                Buka <b>WhatsApp</b> di HP > <b>Perangkat Tertaut</b> > <b>Tautkan Perangkat</b> > Arahkan kamera ke QR di atas.
            </p>
        </div>

        {{-- TAB 2: Pairing Code --}}
        <div id="modal-tab-code" class="hidden space-y-3 text-left">
            <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 space-y-2">
                <label class="block text-xs font-semibold text-gray-700">Nomor WhatsApp Admin:</label>
                <div class="flex gap-2">
                    <input id="modal-pairing-phone" type="text" placeholder="08123456789" class="flex-1 px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#1A3B32]">
                    <button onclick="requestModalPairingCode()" id="modal-btn-get-code" class="bg-[#1A3B32] hover:bg-[#132c25] text-white px-4 py-2.5 rounded-xl text-xs font-semibold transition">
                        Dapatkan Kode
                    </button>
                </div>
            </div>

            <div id="modal-code-result" class="hidden p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-center space-y-2">
                <span class="text-xs text-emerald-700 font-medium">Masukkan kode ini di WhatsApp HP Anda:</span>
                <div id="modal-display-code" class="text-2xl font-mono font-bold tracking-widest text-[#1A3B32] py-2 bg-white rounded-xl border border-emerald-300 select-all cursor-pointer">
                    ----
                </div>
                <p class="text-[11px] text-gray-500">Buka WA di HP > <b>Perangkat Tertaut</b> > <b>Tautkan dengan nomor telepon saja</b></p>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex gap-2 mt-4 pt-3 border-t border-gray-100">
            <button onclick="resetWaSession()" class="flex-1 text-xs bg-red-50 text-red-600 hover:bg-red-100 py-2 px-3 rounded-xl transition font-medium flex items-center justify-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-3.5 h-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                <span>Reset Sesi</span>
            </button>
            <button onclick="closeWaModal()" class="flex-1 text-xs bg-gray-100 text-gray-700 hover:bg-gray-200 py-2 px-3 rounded-xl transition font-medium">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
let modalQrInterval = null;
let modalCountdownInterval = null;
let modalCountdownValue = 20;

function checkWaStatus() {
    fetch('{{ route("admin.whatsapp.status") }}')
        .then(res => res.json())
        .then(data => {
            const dot = document.getElementById('wa-status-dot');
            const text = document.getElementById('wa-status-text');
            const qrBtn = document.getElementById('wa-qr-btn');

            if (data.status === 'connected' || data.connected === true) {
                dot.className = 'w-2.5 h-2.5 rounded-full bg-emerald-400';
                
                let userDisplay = '';
                if (typeof data.user === 'string' && data.user) {
                    userDisplay = data.user;
                } else if (data.user && typeof data.user === 'object') {
                    let rawPhone = data.user.id ? data.user.id.split('@')[0].split(':')[0] : (data.user.phone || '');
                    if (rawPhone.startsWith('62')) {
                        rawPhone = '0' + rawPhone.substring(2);
                    }
                    userDisplay = rawPhone || data.user.name || '';
                } else if (data.account) {
                    userDisplay = data.account;
                }
                
                text.textContent = 'Terhubung' + (userDisplay ? ` (${userDisplay})` : '');
                text.className = 'text-xs text-emerald-300 font-medium truncate';
                qrBtn.classList.add('hidden');
                
                const qrContainer = document.getElementById('wa-qr-container');
                if (qrContainer && !qrContainer.querySelector('img')) {
                    qrContainer.innerHTML = '<div class="text-center py-6"><div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-2"><i class="fa-brands fa-whatsapp text-emerald-600 text-2xl"></i></div><p class="text-sm font-bold text-gray-900">WhatsApp Sudah Terhubung!</p></div>';
                }
            } else if (data.status === 'qr_ready') {
                dot.className = 'w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse';
                text.textContent = 'Menunggu Scan';
                text.className = 'text-xs text-amber-300 font-medium';
                qrBtn.classList.remove('hidden');
            } else {
                dot.className = 'w-2.5 h-2.5 rounded-full bg-red-400';
                text.textContent = 'Terputus';
                text.className = 'text-xs text-red-300 font-medium';
                qrBtn.classList.remove('hidden');
            }
        })
        .catch(() => {
            const dot = document.getElementById('wa-status-dot');
            const text = document.getElementById('wa-status-text');
            const qrBtn = document.getElementById('wa-qr-btn');
            if (dot) dot.className = 'w-2.5 h-2.5 rounded-full bg-gray-400';
            if (text) {
                text.textContent = 'Offline';
                text.className = 'text-xs text-gray-400';
            }
            if (qrBtn) qrBtn.classList.remove('hidden');
        });
}

function showWaModal() {
    const modal = document.getElementById('wa-qr-modal');

    // Pindahkan modal ke body agar berada di atas seluruh dashboard
    if (modal.parentElement !== document.body) {
        document.body.appendChild(modal);
    }

    modal.style.display = 'flex';
    modal.classList.remove('hidden');

    loadModalQR();
    startModalCountdown();

    modalQrInterval = setInterval(loadModalQR, 20000);
}

function closeWaModal() {
    const modal = document.getElementById('wa-qr-modal');
    modal.style.display = 'none';
    modal.classList.add('hidden');
    if (modalQrInterval) clearInterval(modalQrInterval);
    if (modalCountdownInterval) clearInterval(modalCountdownInterval);
}

function switchModalTab(tab) {
    const tabQr = document.getElementById('modal-tab-qr');
    const tabCode = document.getElementById('modal-tab-code');
    const btnQr = document.getElementById('modal-tab-qr-btn');
    const btnCode = document.getElementById('modal-tab-code-btn');

    if (tab === 'qr') {
        tabQr.classList.remove('hidden');
        tabCode.classList.add('hidden');
        btnQr.className = 'flex-1 py-1.5 rounded-lg text-xs font-semibold bg-white text-[#1A3B32] shadow-sm transition flex items-center justify-center gap-1.5';
        btnCode.className = 'flex-1 py-1.5 rounded-lg text-xs font-semibold text-gray-500 hover:text-gray-900 transition flex items-center justify-center gap-1.5';
    } else {
        tabQr.classList.add('hidden');
        tabCode.classList.remove('hidden');
        btnCode.className = 'flex-1 py-1.5 rounded-lg text-xs font-semibold bg-white text-[#1A3B32] shadow-sm transition flex items-center justify-center gap-1.5';
        btnQr.className = 'flex-1 py-1.5 rounded-lg text-xs font-semibold text-gray-500 hover:text-gray-900 transition flex items-center justify-center gap-1.5';
    }
}

function loadModalQR() {
    fetch('{{ route("admin.whatsapp.qr") }}')
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('wa-qr-container');
            const timer = document.getElementById('modal-qr-timer');
            if (data.status === 'qr_ready' && data.qr) {
                container.innerHTML = `
                    <img src="${data.qr}" alt="WhatsApp QR Code" class="w-48 h-48 rounded-xl shadow-xs border border-gray-200">
                    <p class="text-xs text-gray-400 mt-2">Pindai QR ini dari WhatsApp HP Anda</p>
                `;
                timer.classList.remove('hidden');
                modalCountdownValue = 20;
            } else if (data.status === 'connected') {
                container.innerHTML = '<div class="text-center py-6"><div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-2"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-6 h-6"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" /></svg></div><p class="text-sm font-bold text-gray-900">WhatsApp Sudah Terhubung</p></div>';
                timer.classList.add('hidden');
                checkWaStatus();
            }
        })
        .catch(() => {
            document.getElementById('wa-qr-container').innerHTML = '<div class="text-center py-6"><p class="text-xs text-red-500">Gagal menghubungi WhatsApp service</p></div>';
        });
}

async function requestModalPairingCode() {
    const phone = document.getElementById('modal-pairing-phone').value.trim();
    if (!phone) {
        alert('Silakan masukkan nomor WhatsApp Anda.');
        return;
    }

    const btn = document.getElementById('modal-btn-get-code');
    btn.disabled = true;
    btn.textContent = 'Meminta...';

    try {
        const res = await fetch('{{ route("admin.whatsapp.pairingCode") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ phone })
        });
        const data = await res.json();
        
        if (data.code) {
            document.getElementById('modal-code-result').classList.remove('hidden');
            document.getElementById('modal-display-code').textContent = data.code;
        } else {
            alert(data.message || 'Gagal membuat pairing code.');
        }
    } catch (e) {
        alert('Terjadi kesalahan jaringan.');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Dapatkan Kode';
    }
}

function resetWaSession() {
    if (!confirm('Apakah Anda yakin ingin mereset sesi WhatsApp?')) return;
    
    const container = document.getElementById('wa-qr-container');
    container.innerHTML = '<div class="animate-spin w-8 h-8 border-4 border-[#1A3B32] border-t-transparent rounded-full mb-2"></div><span class="text-xs text-gray-500">Mereset sesi...</span>';

    fetch('{{ route("admin.whatsapp.reset") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(() => {
        setTimeout(() => {
            loadModalQR();
            checkWaStatus();
        }, 2000);
    });
}

function startModalCountdown() {
    if (modalCountdownInterval) clearInterval(modalCountdownInterval);
    modalCountdownInterval = setInterval(() => {
        modalCountdownValue--;
        const el = document.getElementById('modal-countdown');
        if (el) el.textContent = modalCountdownValue + 's';
        if (modalCountdownValue <= 0) {
            modalCountdownValue = 20;
        }
    }, 1000);
}

// Auto-check on page load
document.addEventListener('DOMContentLoaded', function () {
    checkWaStatus();
    setInterval(checkWaStatus, 10000);
});
</script>
