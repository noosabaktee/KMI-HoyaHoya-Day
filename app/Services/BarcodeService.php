<?php

namespace App\Services;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\Output\QRMarkupSVG;
use Picqer\Barcode\BarcodeGeneratorPNG;

class BarcodeService
{
    /**
     * Generate barcode PNG data as raw string
     */
    public function generateBarcodePng(string $code): string
    {
        try {
            $generator = new BarcodeGeneratorPNG();
            return $generator->getBarcode($code, $generator::TYPE_CODE_128, 2, 60);
        } catch (\Throwable) {
            return '';
        }
    }

    /**
     * Generate QR Code as SVG or data-uri string
     */
    public function generateQrCodeSvg(string $code): string
    {
        $options = new QROptions([
            'outputInterface' => QRMarkupSVG::class,
            'outputBase64' => false,
        ]);
        return (new QRCode($options))->render($code);
    }

    /**
     * Generate a QRIS-Style Vertical Voucher Card Image using PHP GD.
     * Features:
     * - Authentic QRIS Standee layout (Portrait 720x1040 px)
     * - Kalbe Morinaga Indonesia corporate branding + Hoya-Hoya Day Festival 2026
     * - QRIS official header styling with quick-response red & green accents
     * - Prominent Employee Name (bold, merchant-style) & NIK
     * - High-resolution centered QR code with QRIS corner brackets
     * - Clean Nominal Badge (e.g. Rp 50.000)
     * - Minimal, clutter-free instructions
     */
    public function createVoucherCardImage(
        string $employeeNik,
        string $employeeName,
        string $voucherCode,
        float $balance
    ): string {
        $width = 720;
        $height = 1040;

        $image = imagecreatetruecolor($width, $height);
        imagesavealpha($image, true);

        // Color definitions
        $white = imagecolorallocate($image, 255, 255, 255);
        $kmiGreen = imagecolorallocate($image, 0, 133, 66);        // #008542 Kalbe Corporate Green
        $kmiDarkGreen = imagecolorallocate($image, 4, 120, 87);    // #047857 Dark Emerald
        $qrisRed = imagecolorallocate($image, 218, 37, 28);        // #DA251C Official QRIS Red
        $morinagaBlue = imagecolorallocate($image, 2, 132, 199);   // #0284C7 Morinaga Cyan
        $darkText = imagecolorallocate($image, 15, 23, 42);        // #0F172A Slate 900
        $mutedText = imagecolorallocate($image, 100, 116, 139);    // #64748B Slate 500
        $lightBorder = imagecolorallocate($image, 226, 232, 240);  // #E2E8F0 Border
        $badgeBg = imagecolorallocate($image, 236, 253, 245);      // #ECFDF5 Mint
        $badgeBorder = imagecolorallocate($image, 52, 211, 153);   // #34D399 Emerald Border

        // 1. Fill Background White
        imagefilledrectangle($image, 0, 0, $width, $height, $white);

        // 2. Outer Card Border
        imagesetthickness($image, 3);
        imagerectangle($image, 8, 8, $width - 8, $height - 8, $lightBorder);

        // 3. Top Accent Stripes (QRIS Red & Kalbe Green)
        imagefilledrectangle($image, 8, 8, $width - 8, 20, $qrisRed);
        imagefilledrectangle($image, 8, 20, $width - 8, 26, $kmiGreen);

        $boldFont = $this->getBoldFontPath();
        $regFont = $this->getRegularFontPath();

        if ($boldFont && file_exists($boldFont)) {
            // --- WITH TRUETYPE FONTS ---

            // Top Left: QRIS Logo & Subtitle
            imagettftext($image, 28, 0, 45, 80, $darkText, $boldFont, "QRIS");
            imagettftext($image, 8, 0, 46, 95, $qrisRed, $boldFont, "PEMBAYARAN DIGITAL");

            // Top Right: PT Kalbe Morinaga Indonesia Branding
            $bboxKmi = imagettfbbox(13, 0, $boldFont, "PT KALBE MORINAGA INDONESIA");
            $kmiW = abs($bboxKmi[2] - $bboxKmi[0]);
            imagettftext($image, 13, 0, $width - 45 - $kmiW, 65, $kmiGreen, $boldFont, "PT KALBE MORINAGA INDONESIA");

            $bboxFest = imagettfbbox(11, 0, $boldFont, "HOYA-HOYA DAY FESTIVAL 2026");
            $festW = abs($bboxFest[2] - $bboxFest[0]);
            imagettftext($image, 11, 0, $width - 45 - $festW, 90, $morinagaBlue, $boldFont, "HOYA-HOYA DAY FESTIVAL 2026");

            // Header separator line
            imagesetthickness($image, 2);
            imageline($image, 35, 120, $width - 35, 120, $lightBorder);

            // Employee Name & NIK (Merchant Standee Style)
            $this->renderCenteredText($image, 22, 165, $darkText, $boldFont, strtoupper($employeeName), $width);
            $this->renderCenteredText($image, 12, 195, $mutedText, $regFont ?: $boldFont, "NIK: {$employeeNik}  •  VOUCHER KARYAWAN", $width);
            $this->renderCenteredText($image, 10, 218, $mutedText, $regFont ?: $boldFont, "NMID: ID2026-KMI-HOYA-FEST", $width);

            // Center QR Code Frame with QRIS Corner Brackets
            $boxX1 = 120;
            $boxY1 = 245;
            $boxX2 = $width - 120;
            $boxY2 = 245 + ($width - 240); // 480x480 square
            $boxW = $boxX2 - $boxX1;

            imagesetthickness($image, 1);
            imagefilledrectangle($image, $boxX1, $boxY1, $boxX2, $boxY2, $white);
            imagerectangle($image, $boxX1, $boxY1, $boxX2, $boxY2, $lightBorder);

            // Draw QRIS Red Corner L-Brackets
            $bLen = 32;
            imagesetthickness($image, 5);
            // Top-Left
            imageline($image, $boxX1, $boxY1, $boxX1 + $bLen, $boxY1, $qrisRed);
            imageline($image, $boxX1, $boxY1, $boxX1, $boxY1 + $bLen, $qrisRed);
            // Top-Right
            imageline($image, $boxX2, $boxY1, $boxX2 - $bLen, $boxY1, $qrisRed);
            imageline($image, $boxX2, $boxY1, $boxX2, $boxY1 + $bLen, $qrisRed);
            // Bottom-Left
            imageline($image, $boxX1, $boxY2, $boxX1 + $bLen, $boxY2, $qrisRed);
            imageline($image, $boxX1, $boxY2, $boxX1, $boxY2 - $bLen, $qrisRed);
            // Bottom-Right
            imageline($image, $boxX2, $boxY2, $boxX2 - $bLen, $boxY2, $qrisRed);
            imageline($image, $boxX2, $boxY2, $boxX2, $boxY2 - $bLen, $qrisRed);

            // Render High Resolution QR Code
            $this->embedQrCode($image, $voucherCode, $boxX1, $boxY1, $boxW);

            // Subtext Below QR Box (Keep voucher code hidden behind the scenes)
            imagesetthickness($image, 1);
            $this->renderCenteredText($image, 11, $boxY2 + 35, $mutedText, $boldFont, "SCAN QR CODE UNTUK PEMBAYARAN", $width);

            // Nominal Voucher Badge Box
            $badgeY1 = $boxY2 + 55;
            $badgeH = 75;
            $badgeW = 460;
            $badgeX1 = (int)(($width - $badgeW) / 2);
            $badgeX2 = $badgeX1 + $badgeW;
            $badgeY2 = $badgeY1 + $badgeH;

            imagefilledrectangle($image, $badgeX1, $badgeY1, $badgeX2, $badgeY2, $badgeBg);
            imagesetthickness($image, 2);
            imagerectangle($image, $badgeX1, $badgeY1, $badgeX2, $badgeY2, $badgeBorder);

            $this->renderCenteredText($image, 10, $badgeY1 + 25, $kmiGreen, $boldFont, "NOMINAL VOUCHER DIGITAL", $width);
            $this->renderCenteredText($image, 22, $badgeY1 + 60, $kmiDarkGreen, $boldFont, "Rp " . number_format($balance, 0, ',', '.'), $width);

            // Minimal Footer Text
            $this->renderCenteredText($image, 11, $badgeY2 + 45, $mutedText, $regFont ?: $boldFont, "Tunjukkan QR ini ke kasir booth UMKM untuk bertransaksi.", $width);
            $this->renderCenteredText($image, 10, $badgeY2 + 70, $mutedText, $regFont ?: $boldFont, "Satu QR untuk berbagai transaksi • Sisa saldo otomatis tersimpan", $width);
        } else {
            // --- FALLBACK USING GD BUILTIN FONTS ---
            imagestring($image, 5, 45, 50, "QRIS - PEMBAYARAN DIGITAL", $qrisRed);
            imagestring($image, 4, 340, 50, "PT KALBE MORINAGA INDONESIA", $kmiGreen);
            imagestring($image, 3, 340, 70, "HOYA-HOYA DAY FESTIVAL 2026", $morinagaBlue);

            imagesetthickness($image, 1);
            imageline($image, 35, 110, $width - 35, 110, $lightBorder);

            $nameStr = strtoupper($employeeName);
            $nameX = (int)(($width - (strlen($nameStr) * 9)) / 2);
            imagestring($image, 5, max(20, $nameX), 140, $nameStr, $darkText);

            $nikStr = "NIK: {$employeeNik}  •  VOUCHER KARYAWAN";
            $nikX = (int)(($width - (strlen($nikStr) * 8)) / 2);
            imagestring($image, 4, max(20, $nikX), 170, $nikStr, $mutedText);

            $boxX1 = 120;
            $boxY1 = 210;
            $boxX2 = $width - 120;
            $boxY2 = 210 + ($width - 240);
            $boxW = $boxX2 - $boxX1;

            imagefilledrectangle($image, $boxX1, $boxY1, $boxX2, $boxY2, $white);
            imagerectangle($image, $boxX1, $boxY1, $boxX2, $boxY2, $lightBorder);

            $this->embedQrCode($image, $voucherCode, $boxX1, $boxY1, $boxW);

            $codeLabel = "SCAN QR CODE UNTUK PEMBAYARAN";
            $codeX = (int)(($width - (strlen($codeLabel) * 8)) / 2);
            imagestring($image, 3, max(20, $codeX), $boxY2 + 20, $codeLabel, $mutedText);

            $badgeY1 = $boxY2 + 50;
            $badgeH = 70;
            $badgeW = 460;
            $badgeX1 = (int)(($width - $badgeW) / 2);
            $badgeX2 = $badgeX1 + $badgeW;
            $badgeY2 = $badgeY1 + $badgeH;

            imagefilledrectangle($image, $badgeX1, $badgeY1, $badgeX2, $badgeY2, $badgeBg);
            imagerectangle($image, $badgeX1, $badgeY1, $badgeX2, $badgeY2, $badgeBorder);

            $nomStr = "Rp " . number_format($balance, 0, ',', '.');
            $nomX = (int)(($width - (strlen($nomStr) * 9)) / 2);
            imagestring($image, 5, max(20, $nomX), $badgeY1 + 25, $nomStr, $kmiDarkGreen);

            $foot1 = "Tunjukkan QR ini ke kasir booth UMKM untuk transaksi.";
            $foot1X = (int)(($width - (strlen($foot1) * 7)) / 2);
            imagestring($image, 3, max(20, $foot1X), $badgeY2 + 30, $foot1, $mutedText);
        }

        // Bottom Decorative Accent
        imagefilledrectangle($image, 8, $height - 18, $width - 8, $height - 8, $kmiGreen);

        // Save card to public storage
        $folder = storage_path('app/public/vouchers');
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        $filename = 'voucher_' . preg_replace('/[^a-zA-Z0-9_-]/', '', $voucherCode) . '.png';
        $fullPath = $folder . '/' . $filename;

        imagepng($image, $fullPath);
        imagedestroy($image);

        return 'vouchers/' . $filename;
    }

    /**
     * Embed high-res QR code into GD image
     */
    protected function embedQrCode($canvas, string $voucherCode, int $boxX1, int $boxY1, int $boxW): void
    {
        try {
            $options = new QROptions([
                'outputInterface' => QRGdImagePNG::class,
                'outputBase64' => false,
                'scale' => 12,
            ]);
            $qr = new QRCode($options);
            $qrPng = $qr->render($voucherCode);
            $qrImg = imagecreatefromstring($qrPng);

            if ($qrImg) {
                $qrW = imagesx($qrImg);
                $qrH = imagesy($qrImg);
                $pad = 28;
                $targetW = $boxW - ($pad * 2);
                $targetH = $boxW - ($pad * 2);
                $targetX = $boxX1 + $pad;
                $targetY = $boxY1 + $pad;
                imagecopyresampled($canvas, $qrImg, $targetX, $targetY, 0, 0, $targetW, $targetH, $qrW, $qrH);
                imagedestroy($qrImg);
            }
        } catch (\Throwable) {
            // Ignore QR embed failure if any
        }
    }

    /**
     * Helper to render centered TTF text
     */
    protected function renderCenteredText($img, int $size, int $y, int $color, string $font, string $text, int $imgWidth): int
    {
        $bbox = imagettfbbox($size, 0, $font, $text);
        $textWidth = abs($bbox[2] - $bbox[0]);
        $x = (int)(($imgWidth - $textWidth) / 2);
        imagettftext($img, $size, 0, $x, $y, $color, $font, $text);
        return $x;
    }

    /**
     * Locate TrueType bold font
     */
    protected function getBoldFontPath(): ?string
    {
        $candidates = [
            'C:\\Windows\\Fonts\\arialbd.ttf',
            'C:\\Windows\\Fonts\\segoeuib.ttf',
            'C:\\Windows\\Fonts\\calibrib.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf',
        ];

        foreach ($candidates as $font) {
            if (file_exists($font)) {
                return $font;
            }
        }

        return $this->getRegularFontPath();
    }

    /**
     * Locate TrueType regular font
     */
    protected function getRegularFontPath(): ?string
    {
        $candidates = [
            'C:\\Windows\\Fonts\\arial.ttf',
            'C:\\Windows\\Fonts\\segoeui.ttf',
            'C:\\Windows\\Fonts\\calibri.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSans-Regular.ttf',
        ];

        foreach ($candidates as $font) {
            if (file_exists($font)) {
                return $font;
            }
        }

        return null;
    }
}
