<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\LogWhatsApp;
use App\Models\Voucher;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $server;
    protected string $apiKey;
    protected string $sender;
    protected string $footer;

    public function __construct()
    {
        $this->server = rtrim(config('services.mpwa.server', env('MPWA_SERVER', 'https://whatsapp.intconnect.id')), '/');
        $this->apiKey = config('services.mpwa.api_key', env('MPWA_API_KEY', ''));
        $this->sender = config('services.mpwa.sender', env('MPWA_SENDER', ''));
        $this->footer = config('services.mpwa.footer', env('MPWA_FOOTER', 'PT Kalbe Morinaga Indonesia'));
    }

    /**
     * Normalize phone number to Indonesian format (e.g. 6287887504946)
     */
    public static function formatPhone(string $phone): string
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($clean, '08')) {
            $clean = '62' . substr($clean, 1);
        } elseif (str_starts_with($clean, '8')) {
            $clean = '62' . $clean;
        }

        return $clean;
    }

    /**
     * Clean WhatsApp JID (e.g. "6287887504946@s.whatsapp.net", "6287887504946:12@c.us") into clean phone number
     */
    public static function cleanJidToPhone(?string $jid): string
    {
        if (empty($jid)) {
            return '';
        }

        $userPart = explode('@', $jid)[0];
        $userPart = explode(':', $userPart)[0];

        return self::formatPhone($userPart);
    }

    /**
     * Send festival voucher QRIS card image to employee via WhatsApp
     */
    public function sendVoucherNotification(Employee $employee, Voucher $voucher): array
    {
        $phone = self::formatPhone($employee->txtEmployeePhone);
        $nominal = number_format($voucher->floatInitialBalance, 0, ',', '.');

        // Concise, engaging caption for the QRIS card image (Voucher code kept behind the scenes)
        $caption = "Halo *{$employee->txtEmployeeName}*! 🎉\n\n"
            . "Berikut adalah *Voucher Spesial Hoya-Hoya Day Festival 2026* dari *PT Kalbe Morinaga Indonesia* senilai *Rp {$nominal}*.\n\n"
            . "💰 *Saldo Awal:* Rp {$nominal}\n"
            . "📸 *Kartu QR:* Tunjukkan gambar QR standee di atas ke kasir booth UMKM saat memesan/bertransaksi.\n\n"
            . "⚠️ *PENTING KONFIRMASI:*\n"
            . "Mohon balas pesan ini (ketik: *Diterima* atau *OK*) untuk konfirmasi penerimaan dan mengaktifkan voucher Anda sebelum digunakan di booth UMKM.\n\n"
            . "Selamat menikmati festival! 🎊\n"
            . "- Panitia Hoya-Hoya Day Festival KMI -";

        $mediaSent = false;
        $responsePayload = null;
        $publicMediaUrl = null;

        // 1. Check if card image file exists
        if (!empty($voucher->txtBarcodeCardPath)) {
            $fullLocalPath = storage_path('app/public/' . $voucher->txtBarcodeCardPath);
            if (file_exists($fullLocalPath)) {
                $publicMediaUrl = $this->resolvePublicImageUrl($voucher->txtBarcodeCardPath, $fullLocalPath);
            }
        }

        // 2. Attempt to send QRIS Card Image via send-media
        if ($publicMediaUrl) {
            $responsePayload = $this->sendMedia($phone, $publicMediaUrl, $caption);
            if (!empty($responsePayload['status']) && $responsePayload['status'] === true) {
                $mediaSent = true;
            }
        }

        // 3. Fallback to text message if media sending was not successful
        if (!$mediaSent) {
            // Append web link if media couldn't be attached directly
            $fallbackMsg = $caption . "\n\n🔗 *Lihat Kartu QRIS:* " . url('/voucher/' . $voucher->txtVoucherCode);
            $responsePayload = $this->sendMessage($phone, $fallbackMsg);
        }

        // 4. Update voucher status to 'Terkirim'
        $voucher->update([
            'txtStatus' => 'Terkirim',
            'dtmSentAt' => now(),
        ]);

        // 5. Log WhatsApp activity
        LogWhatsApp::create([
            'txtRecipientNumber' => $phone,
            'txtMessage' => $caption,
            'txtDirection' => 'OUTGOING',
            'txtMediaUrl' => $publicMediaUrl ?: url('/voucher/' . $voucher->txtVoucherCode),
            'txtStatus' => ($responsePayload['status'] ?? false) ? 'SENT' : 'PENDING',
            'txtApiResponse' => json_encode($responsePayload),
            'intEmployee_ID' => $employee->intEmployee_ID,
            'intVoucher_ID' => $voucher->intVoucher_ID,
        ]);

        return [
            'success' => true,
            'phone' => $phone,
            'voucher_code' => $voucher->txtVoucherCode,
            'media_sent' => $mediaSent,
            'public_url' => $publicMediaUrl,
            'response' => $responsePayload,
        ];
    }

    /**
     * Resolve a public URL for the image so MPWA cloud server can fetch it
     */
    protected function resolvePublicImageUrl(string $relativePath, string $localFilePath): string
    {
        $appUrl = config('app.url', 'http://localhost');

        // If app is deployed on a public domain (not localhost / 127.0.0.1)
        if (!preg_match('/localhost|127\.0\.0\.1/i', $appUrl)) {
            return asset('storage/' . $relativePath);
        }

        // When running in development/local, upload to freeimage.host for high-speed CDN delivery to MPWA
        try {
            $ch = curl_init('https://freeimage.host/api/1/upload');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'key' => '6d207e02198a847aa98d0a2a901485a5',
                'action' => 'upload',
                'source' => base64_encode(file_get_contents($localFilePath)),
                'format' => 'json',
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($code === 200 && $response) {
                $data = json_decode($response, true);
                $cdnUrl = $data['image']['url'] ?? $data['image']['display_url'] ?? null;
                if ($cdnUrl && filter_var($cdnUrl, FILTER_VALIDATE_URL)) {
                    return $cdnUrl;
                }
            }
        } catch (\Throwable $e) {
            Log::warning("Freeimage upload failed: " . $e->getMessage());
        }

        // Fallback to tmpfiles.org
        try {
            $ch = curl_init('https://tmpfiles.org/api/v1/upload');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'file' => new \CURLFile($localFilePath, 'image/png', basename($localFilePath)),
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $result = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($code === 200 && $result) {
                $data = json_decode($result, true);
                if (!empty($data['data']['url'])) {
                    // Convert tmpfiles.org/XXXX to direct link tmpfiles.org/dl/XXXX
                    return str_replace('tmpfiles.org/', 'tmpfiles.org/dl/', $data['data']['url']);
                }
            }
        } catch (\Throwable $e) {
            Log::warning("Tmpfiles upload failed: " . $e->getMessage());
        }

        return asset('storage/' . $relativePath);
    }

    /**
     * Send media message via MPWA POST /send-media
     */
    public function sendMedia(string $number, string $mediaUrl, string $caption = ''): array
    {
        $url = $this->server . '/send-media';

        try {
            $response = Http::asForm()->timeout(25)->post($url, [
                'api_key' => $this->apiKey,
                'sender' => $this->sender,
                'number' => $number,
                'media_type' => 'image',
                'url' => $mediaUrl,
                'caption' => $caption,
            ]);

            $data = $response->json() ?? ['raw' => $response->body()];
            $isSuccess = $response->successful() && (!isset($data['status']) || $data['status'] === true);

            return [
                'status' => $isSuccess,
                'http_code' => $response->status(),
                'data' => $data,
            ];
        } catch (\Throwable $e) {
            Log::warning("MPWA sendMedia error: " . $e->getMessage());
            return [
                'status' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send standard text message via MPWA
     */
    public function sendMessage(string $number, string $message): array
    {
        $url = $this->server . '/send-message';

        $params = [
            'api_key' => $this->apiKey,
            'sender' => $this->sender,
            'number' => $number,
            'message' => $message,
            'footer' => $this->footer,
        ];

        try {
            $response = Http::timeout(15)->get($url, $params);
            $data = $response->json() ?? ['raw' => $response->body()];
            $isSuccess = $response->successful() && (!isset($data['status']) || $data['status'] === true);

            return [
                'status' => $isSuccess,
                'http_code' => $response->status(),
                'data' => $data,
            ];
        } catch (\Throwable $e) {
            Log::warning("MPWA sendMessage error: " . $e->getMessage());
            return [
                'status' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Handle incoming webhook reply from employee (MPWA Gateway)
     * Payload structure based on official MPWA doc:
     * - 'device': your sender/device number
     * - 'from': the number/JID of the whatsapp sender (e.g. "6287887504946@s.whatsapp.net")
     * - 'participant': sender number if in group
     * - 'message': message text string
     * - 'name': name of the sender
     * - 'media': array with caption, fileName, stream
     */
    public function handleIncomingWebhook(array $payload): array
    {
        // 1. Identify device/bot phone to avoid processing self-sent messages
        $device = (string) ($payload['device'] ?? '');
        $devicePhone = self::cleanJidToPhone($device);

        // 2. Identify incoming sender phone:
        // Priority order: 'participant' (group individual), 'from' (individual sender), then other fallbacks
        $rawSender = null;
        $candidates = [
            $payload['participant'] ?? null,
            $payload['from'] ?? null,
            $payload['number'] ?? null,
            $payload['sender_phone'] ?? null,
            $payload['data']['from'] ?? null,
        ];

        foreach ($candidates as $cand) {
            if (!empty($cand) && is_string($cand) && trim($cand) !== '') {
                $rawSender = trim($cand);
                break;
            }
        }

        // If not found and 'sender' is present and different from device:
        if (!$rawSender && !empty($payload['sender']) && is_string($payload['sender'])) {
            $senderClean = self::cleanJidToPhone($payload['sender']);
            if ($senderClean !== $devicePhone) {
                $rawSender = $payload['sender'];
            }
        }

        if (!$rawSender) {
            return [
                'status' => false,
                'message' => 'Sender number not found in webhook payload',
            ];
        }

        $phone = self::cleanJidToPhone($rawSender);

        if (empty($phone) || strlen($phone) < 7) {
            return [
                'status' => false,
                'message' => 'Invalid sender phone number: ' . $rawSender,
            ];
        }

        // Ignore messages sent by our own device (bot loop prevention)
        if (!empty($devicePhone) && $phone === $devicePhone) {
            return [
                'status' => true,
                'message' => 'Ignored message from device itself',
            ];
        }

        if (!empty($payload['fromMe']) && ($payload['fromMe'] === true || $payload['fromMe'] === 'true' || $payload['fromMe'] === 1)) {
            return [
                'status' => true,
                'message' => 'Ignored outgoing message (fromMe)',
            ];
        }

        // 3. Extract text content
        $messageText = $payload['message'] 
            ?? $payload['media']['caption'] 
            ?? $payload['caption'] 
            ?? $payload['data']['message'] 
            ?? $payload['body'] 
            ?? '';

        if (is_array($messageText)) {
            $messageText = $messageText['conversation'] 
                ?? $messageText['text'] 
                ?? $messageText['caption'] 
                ?? json_encode($messageText);
        }

        // 4. Find employee by phone:
        // Try exact match, local prefix 08..., or last 9 digits suffix
        $suffix9 = substr($phone, -9);
        $local08 = '0' . substr($phone, 2);

        $employee = Employee::where('txtEmployeePhone', $phone)
            ->orWhere('txtEmployeePhone', $local08)
            ->orWhere(function ($q) use ($suffix9) {
                if (strlen($suffix9) >= 7) {
                    $q->where('txtEmployeePhone', 'LIKE', '%' . $suffix9);
                }
            })
            ->first();

        // Fallback: If not found by phone, check if the employee replied with their voucher code or NIK
        if (!$employee && !empty($messageText) && is_string($messageText)) {
            if (preg_match('/HY26-[A-Z0-9-]+/i', $messageText, $matches)) {
                $v = Voucher::where('txtVoucherCode', strtoupper($matches[0]))->first();
                if ($v) {
                    $employee = $v->employee;
                }
            }

            if (!$employee && preg_match('/^\d{4,10}$/', trim($messageText))) {
                $employee = Employee::where('txtEmployeeNik', trim($messageText))->first();
            }
        }

        // 5. Activate voucher if found
        $activated = false;
        $voucher = null;

        if ($employee) {
            $voucher = Voucher::where('intEmployee_ID', $employee->intEmployee_ID)->first();
            if ($voucher && in_array($voucher->txtStatus, ['Dibuat', 'Terkirim'])) {
                $voucher->update([
                    'txtStatus' => 'Aktif',
                    'dtmActivatedAt' => now(),
                ]);
                $activated = true;

                // Send confirmation reply back to employee
                try {
                    $confirmMsg = "Halo *{$employee->txtEmployeeName}*, terima kasih atas konfirmasinya! 🎉\n\n"
                        . "Voucher Belanja Hoya-Hoya Day Festival Anda sekarang telah *AKTIF*.\n"
                        . "💰 Sisa Saldo: *Rp " . number_format($voucher->floatCurrentBalance, 0, ',', '.') . "*.\n\n"
                        . "Silakan tunjukkan QR voucher Anda saat berbelanja di booth UMKM festival!";

                    $this->sendMessage($phone, $confirmMsg);
                } catch (\Throwable $e) {
                    Log::warning("Failed to send WhatsApp confirmation: " . $e->getMessage());
                }
            }
        }

        // 6. Log WhatsApp incoming event
        LogWhatsApp::create([
            'txtRecipientNumber' => $phone,
            'txtMessage' => is_string($messageText) ? $messageText : json_encode($messageText),
            'txtDirection' => 'INCOMING',
            'txtStatus' => 'RECEIVED',
            'txtApiResponse' => json_encode($payload),
            'intEmployee_ID' => $employee?->intEmployee_ID,
            'intVoucher_ID' => $voucher?->intVoucher_ID,
        ]);

        return [
            'status' => true,
            'employee_found' => (bool) $employee,
            'voucher_activated' => $activated,
            'employee_name' => $employee?->txtEmployeeName,
            'voucher_code' => $voucher?->txtVoucherCode,
            'current_status' => $voucher?->fresh()->txtStatus,
        ];
    }
}
