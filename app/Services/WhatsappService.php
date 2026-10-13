<?php

namespace App\Services;

use App\Models\WhatsappLog;
use App\Models\Booking;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    /**
     * Base URL of the WhatsApp Node.js service
     */
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.whatsapp.url', 'http://localhost:3000'), '/');
    }

    /**
     * Normalize phone number to Indonesian format (628xxx)
     */
    public static function normalizePhone(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        // Strip all non-digit characters
        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        // Handle various Indonesian prefixes
        if (str_starts_with($cleaned, '0')) {
            $cleaned = '62' . substr($cleaned, 1);
        } elseif (!str_starts_with($cleaned, '62')) {
            $cleaned = '62' . $cleaned;
        }

        // Validate length: 62 + minimum 8 digits = 10
        if (strlen($cleaned) < 10 || strlen($cleaned) > 15) {
            return null;
        }

        return $cleaned;
    }

    /**
     * Check if WhatsApp service is available and connected
     */
    public function getStatus(): array
    {
        try {
            $response = Http::timeout(5)->get($this->baseUrl . '/status');

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'status' => 'error',
                'connected' => false,
                'message' => 'Service returned non-200',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'offline',
                'connected' => false,
                'message' => 'WhatsApp service tidak dapat dihubungi: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check if WhatsApp service is connected
     */
    public function isConnected(): bool
    {
        $status = $this->getStatus();
        return ($status['connected'] ?? false) === true;
    }

    /**
     * Get QR code data from the service
     */
    public function getQR(): array
    {
        try {
            $response = Http::timeout(5)->get($this->baseUrl . '/qr');

            if ($response->successful()) {
                return $response->json();
            }

            return ['status' => 'error', 'message' => 'Gagal mengambil QR Code'];
        } catch (\Exception $e) {
            return ['status' => 'offline', 'message' => 'Service tidak dapat dihubungi'];
        }
    }

    /**
     * Send a WhatsApp notification and log to database
     *
     * @param Booking $booking
     * @param string  $type     One of: konfirmasi, approved, rejected, completed, reminder_h1
     * @param string  $message  The message text to send
     * @return bool   Whether the message was sent successfully
     */
    public function sendNotification(Booking $booking, string $type, string $message): bool
    {
        $phone = self::normalizePhone($booking->user->no_whatsapp ?? null);

        if (!$phone) {
            Log::warning("WA Notification skipped: no valid phone for booking #{$booking->id}");
            return false;
        }

        // Create log entry (pending)
        $waLog = WhatsappLog::create([
            'booking_id' => $booking->id,
            'phone' => $phone,
            'message' => $message,
            'type' => $type,
            'status' => 'pending',
            'retry_count' => 0,
        ]);

        // Attempt to send
        return $this->attemptSend($waLog, $phone, $message);
    }

    /**
     * Attempt to send message with retry
     */
    protected function attemptSend(WhatsappLog $waLog, string $phone, string $message, int $maxRetries = 1): bool
    {
        $attempt = 0;

        while ($attempt <= $maxRetries) {
            try {
                $response = Http::timeout(15)
                    ->retry(0) // We handle retries manually
                    ->post($this->baseUrl . '/send-message', [
                        'phone' => $phone,
                        'message' => $message,
                    ]);

                $responseData = $response->json();

                if ($response->successful() && ($responseData['success'] ?? false)) {
                    $waLog->update([
                        'status' => 'terkirim',
                        'response' => json_encode($responseData),
                        'sent_at' => now(),
                        'retry_count' => $attempt,
                    ]);

                    Log::info("WA Notification sent to {$phone} for booking #{$waLog->booking_id} (type: {$waLog->type})");
                    return true;
                }

                // Non-successful response
                $errorMsg = $responseData['error'] ?? 'Unknown error';
                Log::warning("WA send attempt #{$attempt} failed for {$phone}: {$errorMsg}");

            } catch (\Exception $e) {
                Log::warning("WA send attempt #{$attempt} exception for {$phone}: " . $e->getMessage());
            }

            $attempt++;

            if ($attempt <= $maxRetries) {
                // Brief pause before retry
                usleep(500000); // 0.5 second
            }
        }

        // All attempts failed
        $waLog->update([
            'status' => 'gagal',
            'response' => 'All send attempts failed after ' . ($maxRetries + 1) . ' tries',
            'retry_count' => $maxRetries + 1,
        ]);

        Log::error("WA Notification FAILED for booking #{$waLog->booking_id} to {$phone}");
        return false;
    }

    /**
     * Build approval notification message
     */
    public static function buildApprovalMessage(Booking $booking): string
    {
        $booking->loadMissing(['user', 'services', 'slot', 'stylist']);

        $tanggal = \Carbon\Carbon::parse($booking->tanggal_kunjungan)->translatedFormat('d F Y');
        $jamMulai = $booking->jam_kunjungan
            ? \Carbon\Carbon::parse($booking->jam_kunjungan)->format('H:i')
            : null;
        $jamSelesai = $booking->jam_kunjungan
            ? \Carbon\Carbon::parse($booking->jam_kunjungan)
                ->addMinutes((int) ($booking->durasi_total_menit ?: $booking->services->sum('pivot.durasi_menit')))
                ->format('H:i')
            : null;
        $bookingId = '#' . str_pad($booking->id, 5, '0', STR_PAD_LEFT);
        $bookingLink = url('/booking');
        $serviceNames = $booking->services->pluck('nama_layanan')->implode(', ');

        return "Halo {$booking->user->name},\n\n"
             . "Reservasi Anda telah DISETUJUI.\n\n"
             . "ID Reservasi: {$bookingId}\n"
             . "Layanan: {$serviceNames}\n"
             . "Stylist: " . ($booking->stylist?->nama ?? $booking->slot?->kapster ?? '-') . "\n"
             . "Jadwal: {$tanggal}, " . ($jamMulai ? "pukul {$jamMulai} - {$jamSelesai} WIB" : "jam akan ditentukan oleh admin") . "\n\n"
             . "Silakan datang tepat waktu dan pastikan Anda menyiapkan kebutuhan perawatan Anda.\n\n"
             . "Klik link berikut untuk melihat halaman reservasi Anda: {$bookingLink}\n\n"
             . "Kami tunggu kehadiran Anda di Alan's Salon.\n"
             . "Terima kasih.";
    }

    /**
     * Build rejection notification message
     */
    public static function buildRejectionMessage(Booking $booking): string
    {
        $bookingId = '#' . str_pad($booking->id, 5, '0', STR_PAD_LEFT);
        $bookingLink = url('/booking');

        return "Halo {$booking->user->name},\n\n"
             . "Mohon maaf, reservasi Anda {$bookingId} tidak dapat kami lanjutkan.\n"
             . "Status reservasi telah DIBATALKAN oleh admin.\n\n"
             . "Untuk informasi lebih lanjut atau jika ada pertanyaan, silakan hubungi kami kembali.\n"
             . "Anda juga dapat membuka halaman reservasi di sini: {$bookingLink}\n\n"
             . "Terima kasih atas pemahaman Anda.\n"
             . "Alan's Salon";
    }

    /**
     * Build completion / review notification message
     */
    public static function buildCompletionMessage(Booking $booking): string
    {
        $bookingLink = url('/booking');

        return "Halo {$booking->user->name},\n\n"
             . "Terima kasih telah menggunakan layanan di Alan's Salon.\n"
             . "Kami sangat senang dapat melayani Anda hari ini.\n\n"
             . "Silakan berikan ulasan Anda melalui dashboard atau buka halaman reservasi berikut: {$bookingLink}\n"
             . "Review Anda sangat berarti bagi kami.\n\n"
             . "Semoga bertemu kembali di Alan's Salon.\n"
             . "Terima kasih.";
    }

    public static function buildCancellationMessage(Booking $booking): string
    {
        $customerName = $booking->user?->name ?? $booking->nama_pelanggan ?? 'Pelanggan';
        $bookingCode = str_pad((string) $booking->id, 5, '0', STR_PAD_LEFT);

        return "Halo {$customerName}, reservasi #{$bookingCode} di Alan's Salon telah dibatalkan. Pembatalan tidak dapat dilakukan refund. Terima kasih.";
    }

    /**
     * Request an 8-digit WhatsApp pairing code for linking by phone number without scanning QR
     */
    public function requestPairingCode(string $phone): array
    {
        try {
            $response = Http::timeout(10)->post($this->baseUrl . '/pairing-code', [
                'phone' => $phone,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return ['success' => false, 'error' => $response->json('error') ?? 'Gagal membuat kode pairing'];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Service tidak dapat dihubungi: ' . $e->getMessage()];
        }
    }

    /**
     * Reset WhatsApp session
     */
    public function resetSession(): array
    {
        try {
            $response = Http::timeout(10)->post($this->baseUrl . '/reset-session');

            if ($response->successful()) {
                return $response->json();
            }

            return ['success' => false, 'message' => 'Gagal reset sesi'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Service tidak dapat dihubungi: ' . $e->getMessage()];
        }
    }
}
