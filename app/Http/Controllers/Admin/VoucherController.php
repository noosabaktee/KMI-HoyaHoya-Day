<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LogActivity;
use App\Models\Penjualan;
use App\Models\Voucher;
use App\Services\BarcodeService;
use App\Services\VoucherService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VoucherController extends Controller
{
    public function __construct(
        protected VoucherService $voucherService,
        protected BarcodeService $barcodeService,
        protected WhatsAppService $whatsAppService
    ) {}

    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $query = Employee::with('voucher')
            ->where('bitActive', 1);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('txtEmployeeNik', 'LIKE', "%{$search}%")
                  ->orWhere('txtEmployeeName', 'LIKE', "%{$search}%")
                  ->orWhere('txtEmployeePhone', 'LIKE', "%{$search}%")
                  ->orWhereHas('voucher', function ($vq) use ($search) {
                      $vq->where('txtVoucherCode', 'LIKE', "%{$search}%");
                  });
            });
        }

        if (!empty($status)) {
            $query->whereHas('voucher', function ($vq) use ($status) {
                $vq->where('txtStatus', $status);
            });
        }

        $employees = $query->orderBy('intEmployee_ID', 'desc')->paginate(15)->withQueryString();

        $stats = [
            'total' => Voucher::count(),
            'dibuat' => Voucher::where('txtStatus', 'Dibuat')->count(),
            'terkirim' => Voucher::where('txtStatus', 'Terkirim')->count(),
            'aktif' => Voucher::where('txtStatus', 'Aktif')->count(),
            'habis' => Voucher::where('txtStatus', 'Habis')->count(),
            'total_saldo_awal' => (float) Voucher::sum('floatInitialBalance'),
            'total_sisa_saldo' => (float) Voucher::sum('floatCurrentBalance'),
        ];

        $departments = Department::where('bitActive', 1)->orderBy('txtDepartmentName')->get();

        return view('admin.vouchers.index', compact('employees', 'stats', 'search', 'status', 'departments'));
    }

    /**
     * Store single voucher
     */
    public function storeSingle(Request $request)
    {
        if ($request->has('amount')) {
            $rawAmount = preg_replace('/\D/', '', (string) $request->input('amount'));
            $request->merge(['amount' => $rawAmount !== '' ? (float) $rawAmount : null]);
        }

        $validated = $request->validate([
            'nik' => 'required|string|max:50',
            'name' => 'required|string|max:150',
            'phone' => 'required|string|max:30',
            'amount' => 'nullable|numeric|min:1000',
            'department' => 'nullable|string|max:100',
            'send_whatsapp' => 'nullable|boolean',
        ]);

        $sendWa = $request->boolean('send_whatsapp', true);

        try {
            $result = $this->voucherService->generateSingle(
                $validated['nik'],
                $validated['name'],
                $validated['phone'],
                $validated['amount'] ?? null,
                $validated['department'] ?? null,
                $sendWa
            );

            LogActivity::record(
                'GENERATE_VOUCHER',
                "Generate voucher {$result['voucher']->txtVoucherCode} untuk {$validated['name']} ({$validated['nik']})."
            );

            return redirect()->route('admin.vouchers.index')->with('success', "Voucher untuk {$validated['name']} berhasil dibuat " . ($sendWa ? "dan dikirim ke WhatsApp!" : "!"));
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal membuat voucher: ' . $e->getMessage());
        }
    }

    /**
     * Store bulk vouchers from Excel
     */
    public function storeBulk(Request $request)
    {
        if ($request->has('default_amount')) {
            $rawAmount = preg_replace('/\D/', '', (string) $request->input('default_amount'));
            $request->merge(['default_amount' => $rawAmount !== '' ? (float) $rawAmount : null]);
        }

        $request->validate([
            'file_excel' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'default_amount' => 'nullable|numeric|min:1000',
            'send_whatsapp' => 'nullable|boolean',
        ]);

        $file = $request->file('file_excel');
        $amount = $request->input('default_amount');
        $sendWa = $request->boolean('send_whatsapp', true);

        try {
            $result = $this->voucherService->generateBulkFromExcel(
                $file,
                $amount ? (float) $amount : null,
                $sendWa
            );

            LogActivity::record(
                'BULK_GENERATE_VOUCHER',
                "Bulk import Excel: {$result['success_count']} berhasil, {$result['failed_count']} gagal."
            );

            $msg = "Impor selesai! Berhasil: {$result['success_count']} karyawan. Gagal: {$result['failed_count']}.";
            if (!empty($result['errors'])) {
                return redirect()->route('admin.vouchers.index')
                    ->with('warning', $msg . " Catatan: " . implode(' | ', array_slice($result['errors'], 0, 3)));
            }

            return redirect()->route('admin.vouchers.index')->with('success', $msg);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal memproses file Excel: ' . $e->getMessage());
        }
    }

    /**
     * Resend WhatsApp notification
     */
    public function resendWhatsApp(int $id)
    {
        $employee = Employee::with('voucher')->findOrFail($id);
        if (!$employee->voucher) {
            return redirect()->back()->with('error', 'Karyawan belum memiliki voucher.');
        }

        try {
            $res = $this->whatsAppService->sendVoucherNotification($employee, $employee->voucher);
            LogActivity::record('RESEND_WA', "Kirim ulang WhatsApp voucher ke {$employee->txtEmployeeName} ({$employee->txtEmployeePhone}).");

            return redirect()->back()->with('success', "Pesan WhatsApp dan Barcode berhasil dikirim ulang ke {$employee->txtEmployeePhone}.");
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal mengirim pesan WhatsApp: ' . $e->getMessage());
        }
    }

    /**
     * Manually activate voucher
     */
    public function activateManual(int $id)
    {
        $employee = Employee::with('voucher')->findOrFail($id);
        if (!$employee->voucher) {
            return redirect()->back()->with('error', 'Voucher tidak ditemukan.');
        }

        $employee->voucher->update([
            'txtStatus' => 'Aktif',
            'dtmActivatedAt' => now(),
        ]);

        LogActivity::record('MANUAL_ACTIVATE', "Aktivasi manual voucher {$employee->voucher->txtVoucherCode} untuk {$employee->txtEmployeeName}.");

        return redirect()->back()->with('success', "Voucher untuk {$employee->txtEmployeeName} berhasil diaktifkan!");
    }

    /**
     * Download Excel Template for employee import
     */
    public function downloadTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Karyawan');

        // Headers matching data_karyawan.xlsx
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Nama');
        $sheet->setCellValue('C1', 'HP');
        $sheet->setCellValue('D1', 'Departemen');

        // Sample rows
        $sheet->setCellValue('A2', '12345');
        $sheet->setCellValue('B2', 'Rama Nusa Bakti');
        $sheet->setCellValue('C2', '6287887504946');
        $sheet->setCellValue('D2', 'Engineering');

        $sheet->setCellValue('A3', '22345');
        $sheet->setCellValue('B3', 'Eko Satrio');
        $sheet->setCellValue('C3', '6281234567891');
        $sheet->setCellValue('D3', 'Produksi');

        // Style header
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        foreach (['A', 'B', 'C', 'D'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'Template_Data_Karyawan_KMI.xlsx');
    }

    /**
     * Update voucher and employee details
     */
    public function update(Request $request, int $id)
    {
        $employee = Employee::with('voucher')->findOrFail($id);

        if ($request->has('current_balance')) {
            $raw = preg_replace('/\D/', '', (string) $request->input('current_balance'));
            $request->merge(['current_balance' => $raw !== '' ? (float) $raw : null]);
        }
        if ($request->has('initial_balance')) {
            $raw = preg_replace('/\D/', '', (string) $request->input('initial_balance'));
            $request->merge(['initial_balance' => $raw !== '' ? (float) $raw : null]);
        }

        $validated = $request->validate([
            'nik' => 'required|string|max:50|unique:mEmployee,txtEmployeeNik,' . $id . ',intEmployee_ID',
            'name' => 'required|string|max:150',
            'phone' => 'required|string|max:30',
            'department' => 'nullable|string|max:100',
            'current_balance' => 'required|numeric|min:0',
            'initial_balance' => 'nullable|numeric|min:0',
            'status' => 'required|string|in:Dibuat,Terkirim,Aktif,Habis',
        ]);

        $formattedPhone = WhatsAppService::formatPhone($validated['phone']);

        $employee->update([
            'txtEmployeeNik' => trim($validated['nik']),
            'txtEmployeeName' => trim($validated['name']),
            'txtEmployeePhone' => $formattedPhone,
            'txtDepartment' => $validated['department'] ?? null,
        ]);

        if ($voucher = $employee->voucher) {
            $initialBal = $validated['initial_balance'] ?? $voucher->floatInitialBalance;
            $currentBal = (float) $validated['current_balance'];

            $voucher->update([
                'floatInitialBalance' => $initialBal,
                'floatCurrentBalance' => $currentBal,
                'txtStatus' => $validated['status'],
                'dtmActivatedAt' => $validated['status'] === 'Aktif' ? ($voucher->dtmActivatedAt ?? now()) : $voucher->dtmActivatedAt,
            ]);

            // Regenerate voucher card image so display matches new name/NIK/balance
            $cardPath = $this->barcodeService->createVoucherCardImage(
                $employee->txtEmployeeNik,
                $employee->txtEmployeeName,
                $voucher->txtVoucherCode,
                $currentBal
            );
            $voucher->update(['txtBarcodeCardPath' => $cardPath]);
        }

        LogActivity::record('UPDATE_VOUCHER', "Memperbarui data voucher & karyawan {$employee->txtEmployeeName} ({$employee->txtEmployeeNik}).");

        return redirect()->route('admin.vouchers.index')->with('success', "Data voucher {$employee->txtEmployeeName} berhasil diperbarui!");
    }

    /**
     * Delete voucher and employee
     */
    public function destroy(int $id)
    {
        $employee = Employee::with('voucher')->findOrFail($id);
        $voucher = $employee->voucher;
        $name = $employee->txtEmployeeName;

        if ($voucher) {
            $hasSales = Penjualan::where('intVoucher_ID', $voucher->intVoucher_ID)->exists();
            if ($hasSales) {
                // If transactions exist, soft delete/deactivate
                $voucher->update(['bitActive' => 0]);
                $employee->update(['bitActive' => 0]);

                LogActivity::record('DEACTIVATE_VOUCHER', "Menonaktifkan voucher {$voucher->txtVoucherCode} penerima {$name} karena memiliki transaksi.");

                return redirect()->route('admin.vouchers.index')
                    ->with('warning', "Voucher {$name} dinonaktifkan (bukan dihapus) karena telah memiliki riwayat transaksi di festival.");
            }

            // If no transactions exist, safely delete barcode card image
            if ($voucher->txtBarcodeCardPath && Storage::disk('public')->exists($voucher->txtBarcodeCardPath)) {
                Storage::disk('public')->delete($voucher->txtBarcodeCardPath);
            }

            $voucher->delete();
        }

        $employee->delete();

        LogActivity::record('DELETE_VOUCHER', "Menghapus data voucher & karyawan {$name}.");

        return redirect()->route('admin.vouchers.index')->with('success', "Data voucher {$name} berhasil dihapus.");
    }
}

