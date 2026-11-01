<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $merchantId = $request->input('merchant_id');
        $paymentType = $request->input('payment_type');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = Penjualan::with(['merchant', 'employee', 'user'])
            ->orderByDesc('dtmInserted');

        if (!empty($merchantId)) {
            $query->where('intMerchant_ID', $merchantId);
        }

        if (!empty($paymentType)) {
            $query->where('txtPaymentType', $paymentType);
        }

        if (!empty($dateFrom)) {
            $query->whereDate('dtmInserted', '>=', $dateFrom);
        }

        if (!empty($dateTo)) {
            $query->whereDate('dtmInserted', '<=', $dateTo);
        }

        $sales = $query->paginate(20)->withQueryString();

        $summary = [
            'total_sales' => (float) (clone $query)->sum('floatTotalAmount'),
            'total_voucher' => (float) (clone $query)->sum('floatVoucherAmount'),
            'total_cash' => (float) (clone $query)->sum('floatCashAmount'),
            'total_count' => (clone $query)->count(),
        ];

        $merchants = Merchant::where('bitActive', 1)->get();

        return view('admin.reports.index', compact('sales', 'summary', 'merchants', 'merchantId', 'paymentType', 'dateFrom', 'dateTo'));
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        $query = Penjualan::with(['merchant', 'employee', 'user'])->orderBy('dtmInserted');

        if ($request->filled('merchant_id')) {
            $query->where('intMerchant_ID', $request->input('merchant_id'));
        }
        if ($request->filled('payment_type')) {
            $query->where('txtPaymentType', $request->input('payment_type'));
        }

        $sales = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Penjualan Festival');

        // Header Title
        $sheet->setCellValue('A1', 'LAPORAN TRANSAKSI & PERPUTARAN EKONOMI');
        $sheet->setCellValue('A2', 'HOYA-HOYA DAY FESTIVAL 2026 - PT KALBE MORINAGA INDONESIA');
        $sheet->setCellValue('A3', 'Dicetak pada: ' . date('d-m-Y H:i:s'));

        // Table Columns
        $headers = [
            'A5' => 'No',
            'B5' => 'No Invoice',
            'C5' => 'Tanggal & Waktu',
            'D5' => 'Booth UMKM',
            'E5' => 'Nama Karyawan',
            'F5' => 'NIK Karyawan',
            'G5' => 'Metode Bayar',
            'H5' => 'Total Belanja (Rp)',
            'I5' => 'Dibayar Voucher (Rp)',
            'J5' => 'Dibayar Tunai (Rp)',
            'K5' => 'Kembalian (Rp)',
            'L5' => 'Status Klaim UMKM',
        ];

        foreach ($headers as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }

        $sheet->getStyle('A5:L5')->getFont()->setBold(true);

        $rowNum = 6;
        $no = 1;
        $grandTotal = 0;
        $grandVoucher = 0;
        $grandCash = 0;

        foreach ($sales as $s) {
            $sheet->setCellValue("A{$rowNum}", $no++);
            $sheet->setCellValue("B{$rowNum}", $s->txtInvoiceNumber);
            $sheet->setCellValue("C{$rowNum}", $s->dtmInserted->format('d/m/Y H:i'));
            $sheet->setCellValue("D{$rowNum}", $s->merchant?->txtMerchantName ?? '-');
            $sheet->setCellValue("E{$rowNum}", $s->employee?->txtEmployeeName ?? 'Umum/Tunai');
            $sheet->setCellValue("F{$rowNum}", $s->employee?->txtEmployeeNik ?? '-');
            $sheet->setCellValue("G{$rowNum}", $s->txtPaymentType);
            $sheet->setCellValue("H{$rowNum}", $s->floatTotalAmount);
            $sheet->setCellValue("I{$rowNum}", $s->floatVoucherAmount);
            $sheet->setCellValue("J{$rowNum}", $s->floatCashAmount);
            $sheet->setCellValue("K{$rowNum}", $s->floatChangeAmount);
            $sheet->setCellValue("L{$rowNum}", $s->bitIsClaimed ? 'Sudah Diklaim' : 'Belum Diklaim');

            $grandTotal += $s->floatTotalAmount;
            $grandVoucher += $s->floatVoucherAmount;
            $grandCash += $s->floatCashAmount;
            $rowNum++;
        }

        // Totals row
        $sheet->setCellValue("G{$rowNum}", 'TOTAL:');
        $sheet->setCellValue("H{$rowNum}", $grandTotal);
        $sheet->setCellValue("I{$rowNum}", $grandVoucher);
        $sheet->setCellValue("J{$rowNum}", $grandCash);
        $sheet->getStyle("G{$rowNum}:L{$rowNum}")->getFont()->setBold(true);

        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'Laporan_Hoya_Hoya_Day_Festival_' . date('Ymd_His') . '.xlsx');
    }
}

