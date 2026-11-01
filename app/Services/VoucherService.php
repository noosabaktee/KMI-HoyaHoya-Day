<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Merchant;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class VoucherService
{
    public function __construct(
        protected BarcodeService $barcodeService,
        protected WhatsAppService $whatsAppService
    ) {}

    /**
     * Generate voucher for a single employee and send via WhatsApp
     */
    public function generateSingle(
        string $nik,
        string $name,
        string $phone,
        ?float $amount = null,
        ?string $department = null,
        bool $sendWhatsApp = true
    ): array {
        $amount = $amount ?? (float) env('DEFAULT_VOUCHER_AMOUNT', 50000);

        return DB::transaction(function () use ($nik, $name, $phone, $amount, $department, $sendWhatsApp) {
            $formattedPhone = WhatsAppService::formatPhone($phone);

            // Find or create employee
            $employee = Employee::firstOrNew(['txtEmployeeNik' => trim($nik)]);
            $employee->txtEmployeeName = trim($name);
            $employee->txtEmployeePhone = $formattedPhone;
            if ($department) {
                $employee->txtDepartment = $department;
            }
            $employee->bitActive = 1;
            $employee->save();

            // Find or create voucher for this employee
            $voucher = Voucher::where('intEmployee_ID', $employee->intEmployee_ID)->first();
            $isNewVoucher = false;

            if (!$voucher) {
                $isNewVoucher = true;
                $voucherCode = 'HY26-' . preg_replace('/[^a-zA-Z0-9]/', '', $employee->txtEmployeeNik) . '-' . strtoupper(Str::random(4));

                $voucher = new Voucher();
                $voucher->intEmployee_ID = $employee->intEmployee_ID;
                $voucher->txtVoucherCode = $voucherCode;
                $voucher->floatInitialBalance = $amount;
                $voucher->floatCurrentBalance = $amount;
                $voucher->txtStatus = 'Dibuat';
                $voucher->bitActive = 1;
            } else {
                // Refresh code or update balance if not used yet
                if ($voucher->floatCurrentBalance == $voucher->floatInitialBalance) {
                    $voucher->floatInitialBalance = $amount;
                    $voucher->floatCurrentBalance = $amount;
                }
            }

            // Create Barcode Card Image
            $cardPath = $this->barcodeService->createVoucherCardImage(
                $employee->txtEmployeeNik,
                $employee->txtEmployeeName,
                $voucher->txtVoucherCode,
                $voucher->floatCurrentBalance
            );

            $voucher->txtBarcodeCardPath = $cardPath;
            $voucher->save();

            $waResult = null;
            if ($sendWhatsApp) {
                $waResult = $this->whatsAppService->sendVoucherNotification($employee, $voucher);
            }

            return [
                'success' => true,
                'employee' => $employee,
                'voucher' => $voucher,
                'wa_result' => $waResult,
            ];
        });
    }

    /**
     * Bulk generate vouchers from Excel file
     */
    public function generateBulkFromExcel(UploadedFile $file, ?float $amount = null, bool $sendWhatsApp = true): array
    {
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        if (count($rows) < 2) {
            throw new \Exception('File Excel tidak berisi data yang valid.');
        }

        $header = array_map(function ($col) {
            return strtolower(trim((string) $col));
        }, $rows[0]);

        // Map column indices
        $idIdx = -1;
        $nameIdx = -1;
        $phoneIdx = -1;
        $deptIdx = -1;

        foreach ($header as $i => $col) {
            if ($idIdx === -1 && (str_contains($col, 'id') || str_contains($col, 'nik') || str_contains($col, 'nomor') || str_contains($col, 'no'))) {
                $idIdx = $i;
            } elseif ($nameIdx === -1 && (str_contains($col, 'nama') || str_contains($col, 'name'))) {
                $nameIdx = $i;
            } elseif ($phoneIdx === -1 && (str_contains($col, 'hp') || str_contains($col, 'phone') || str_contains($col, 'wa') || str_contains($col, 'telp') || str_contains($col, 'kontak'))) {
                $phoneIdx = $i;
            } elseif ($deptIdx === -1 && (str_contains($col, 'dept') || str_contains($col, 'departemen') || str_contains($col, 'divisi') || str_contains($col, 'bagian'))) {
                $deptIdx = $i;
            }
        }

        // Fallback default index if header names are generic
        if ($idIdx === -1) $idIdx = 0;
        if ($nameIdx === -1) $nameIdx = 1;
        if ($phoneIdx === -1) $phoneIdx = 2;

        $processed = 0;
        $success = 0;
        $failed = 0;
        $errors = [];
        $records = [];

        for ($r = 1; $r < count($rows); $r++) {
            $row = $rows[$r];
            $nik = trim((string) ($row[$idIdx] ?? ''));
            $name = trim((string) ($row[$nameIdx] ?? ''));
            $phone = trim((string) ($row[$phoneIdx] ?? ''));
            $dept = $deptIdx !== -1 ? trim((string) ($row[$deptIdx] ?? '')) : null;

            if (empty($nik) && empty($name)) {
                continue; // empty row
            }

            $processed++;

            if (empty($nik) || empty($name) || empty($phone)) {
                $failed++;
                $errors[] = "Baris " . ($r + 1) . ": Data tidak lengkap (ID: '$nik', Nama: '$name', HP: '$phone')";
                continue;
            }

            try {
                $res = $this->generateSingle($nik, $name, $phone, $amount, $dept, $sendWhatsApp);
                $success++;
                $records[] = [
                    'nik' => $nik,
                    'name' => $name,
                    'phone' => $phone,
                    'voucher_code' => $res['voucher']->txtVoucherCode,
                    'status' => $res['voucher']->txtStatus,
                ];
            } catch (\Throwable $e) {
                $failed++;
                $errors[] = "Baris " . ($r + 1) . " ($name): " . $e->getMessage();
            }
        }

        return [
            'total_rows' => $processed,
            'success_count' => $success,
            'failed_count' => $failed,
            'errors' => $errors,
            'records' => $records,
        ];
    }

    /**
     * Check voucher validity and balance for POS scanning
     */
    public function checkVoucherForPos(string $search): array
    {
        $search = trim($search);

        $voucher = Voucher::with('employee')
            ->where(function ($q) use ($search) {
                $q->where('txtVoucherCode', $search)
                    ->orWhere('txtVoucherCode', 'LIKE', "%{$search}%")
                    ->orWhereHas('employee', function ($eq) use ($search) {
                        $eq->where('txtEmployeeNik', $search)
                           ->orWhere('txtEmployeePhone', 'LIKE', "%{$search}%");
                    });
            })
            ->where('bitActive', 1)
            ->first();

        if (!$voucher) {
            return [
                'valid' => false,
                'message' => 'Barcode / Voucher tidak ditemukan dalam sistem.',
            ];
        }

        if ($voucher->floatCurrentBalance <= 0) {
            return [
                'valid' => false,
                'voucher' => $voucher,
                'message' => 'Saldo voucher telah habis (Rp 0). Karyawan harus membayar penuh tunai.',
            ];
        }

        return [
            'valid' => true,
            'voucher_id' => $voucher->intVoucher_ID,
            'voucher_code' => $voucher->txtVoucherCode,
            'employee_name' => $voucher->employee?->txtEmployeeName ?? 'Karyawan KMI',
            'employee_nik' => $voucher->employee?->txtEmployeeNik ?? '-',
            'initial_balance' => (float) $voucher->floatInitialBalance,
            'current_balance' => (float) $voucher->floatCurrentBalance,
            'status' => $voucher->txtStatus,
            'card_url' => $voucher->txtBarcodeCardPath ? asset('storage/' . $voucher->txtBarcodeCardPath) : null,
        ];
    }

    /**
     * Process POS sale with voucher, split, or cash
     */
    public function processSale(
        Merchant $merchant,
        User $cashier,
        array $items,
        ?string $voucherCode = null,
        float $cashGiven = 0,
        ?string $notes = null
    ): Penjualan {
        return DB::transaction(function () use ($merchant, $cashier, $items, $voucherCode, $cashGiven, $notes) {
            // 1. Calculate Total Amount & Validate Stock
            $totalAmount = 0;
            $preparedDetails = [];

            foreach ($items as $item) {
                $productId = $item['id'] ?? $item['product_id'] ?? null;
                $quantity = max(1, (int) ($item['quantity'] ?? $item['qty'] ?? 1));

                $product = Product::where('intProduct_ID', $productId)
                    ->where('intMerchant_ID', $merchant->intMerchant_ID)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($product->intStock > 0 && $product->intStock < $quantity) {
                    throw new \Exception("Stok produk '{$product->txtProductName}' tidak mencukupi (Sisa: {$product->intStock}).");
                }

                $subtotal = $product->floatPrice * $quantity;
                $totalAmount += $subtotal;

                $preparedDetails[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'price' => $product->floatPrice,
                    'subtotal' => $subtotal,
                ];
            }

            if ($totalAmount <= 0) {
                throw new \Exception('Total transaksi tidak boleh Rp 0.');
            }

            // 2. Handle Payment Mode (Voucher, Split, or Pure Cash)
            $voucher = null;
            $voucherAmount = 0.0;
            $cashAmount = 0.0;
            $changeAmount = 0.0;
            $paymentType = 'CASH';

            if (!empty($voucherCode)) {
                $voucher = Voucher::where('txtVoucherCode', trim($voucherCode))
                    ->lockForUpdate()
                    ->first();

                if (!$voucher) {
                    throw new \Exception("Voucher dengan kode '{$voucherCode}' tidak ditemukan.");
                }

                if ($voucher->floatCurrentBalance <= 0) {
                    throw new \Exception("Saldo voucher ini sudah habis (Rp 0).");
                }

                $curBalance = (float) $voucher->floatCurrentBalance;

                if ($curBalance >= $totalAmount) {
                    // Full Voucher Payment
                    $voucherAmount = $totalAmount;
                    $cashAmount = 0.0;
                    $changeAmount = 0.0;
                    $paymentType = 'VOUCHER';
                    $newBalance = $curBalance - $totalAmount;
                } else {
                    // Split Payment: Voucher habis + kekurangan dibayar Cash
                    $voucherAmount = $curBalance;
                    $shortage = $totalAmount - $curBalance;
                    $cashAmount = $shortage;
                    $paymentType = 'SPLIT';
                    $newBalance = 0.0;

                    if ($cashGiven < $cashAmount) {
                        throw new \Exception("Sisa saldo voucher (Rp " . number_format($curBalance, 0, ',', '.') . ") tidak mencukupi total belanja (Rp " . number_format($totalAmount, 0, ',', '.') . "). Karyawan harus membayar kekurangan tunai sebesar Rp " . number_format($cashAmount, 0, ',', '.') . ".");
                    }

                    $changeAmount = $cashGiven - $cashAmount;
                }

                // Update Voucher
                $voucher->floatCurrentBalance = $newBalance;
                if ($newBalance <= 0) {
                    $voucher->txtStatus = 'Habis';
                }
                $voucher->save();
            } else {
                // Pure Cash
                $paymentType = 'CASH';
                $voucherAmount = 0.0;
                $cashAmount = $totalAmount;

                if ($cashGiven < $cashAmount) {
                    throw new \Exception("Uang tunai yang diberikan (Rp " . number_format($cashGiven, 0, ',', '.') . ") kurang dari total belanja (Rp " . number_format($cashAmount, 0, ',', '.') . ").");
                }

                $changeAmount = $cashGiven - $cashAmount;
            }

            // 3. Generate Invoice Number
            $seq = Penjualan::whereDate('dtmInserted', today())->count() + 1;
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad($merchant->intMerchant_ID, 2, '0', STR_PAD_LEFT) . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);

            // 4. Create Penjualan
            $penjualan = Penjualan::create([
                'txtInvoiceNumber' => $invoiceNumber,
                'intMerchant_ID' => $merchant->intMerchant_ID,
                'intUser_ID' => $cashier->intUser_ID,
                'intEmployee_ID' => $voucher?->intEmployee_ID,
                'intVoucher_ID' => $voucher?->intVoucher_ID,
                'floatTotalAmount' => $totalAmount,
                'floatVoucherAmount' => $voucherAmount,
                'floatCashAmount' => $cashAmount,
                'floatChangeAmount' => $changeAmount,
                'txtPaymentType' => $paymentType,
                'txtNotes' => $notes,
                'bitIsClaimed' => 0,
            ]);

            // 5. Create Details and Decrement Stock
            foreach ($preparedDetails as $det) {
                PenjualanDetail::create([
                    'intPenjualan_ID' => $penjualan->intPenjualan_ID,
                    'intProduct_ID' => $det['product']->intProduct_ID,
                    'txtProductName' => $det['product']->txtProductName,
                    'floatPrice' => $det['price'],
                    'intQuantity' => $det['quantity'],
                    'floatSubtotal' => $det['subtotal'],
                ]);

                if ($det['product']->intStock > 0) {
                    $det['product']->decrement('intStock', $det['quantity']);
                }
            }

            // 6. Record Voucher Usage Ledger if voucher used
            if ($voucher && $voucherAmount > 0) {
                VoucherUsage::create([
                    'intVoucher_ID' => $voucher->intVoucher_ID,
                    'intPenjualan_ID' => $penjualan->intPenjualan_ID,
                    'floatAmountUsed' => $voucherAmount,
                    'floatBalanceBefore' => $voucher->floatCurrentBalance + $voucherAmount,
                    'floatBalanceAfter' => $voucher->floatCurrentBalance,
                ]);
            }

            return $penjualan->load(['details', 'merchant', 'employee', 'voucher']);
        });
    }
}

