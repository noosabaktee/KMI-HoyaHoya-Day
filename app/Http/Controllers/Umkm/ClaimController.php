<?php

namespace App\Http\Controllers\Umkm;

use App\Http\Controllers\Controller;
use App\Models\ClaimReimbursement;
use App\Models\ClaimReimbursementDetail;
use App\Models\LogActivity;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClaimController extends Controller
{
    public function index()
    {
        $merchant = Auth::user()->merchant;
        if (!$merchant) abort(403);

        // Calculate unclaimed voucher sales
        $unclaimedSales = Penjualan::where('intMerchant_ID', $merchant->intMerchant_ID)
            ->where('bitIsClaimed', 0)
            ->where('floatVoucherAmount', '>', 0)
            ->get();

        $unclaimedAmount = (float) $unclaimedSales->sum('floatVoucherAmount');
        $unclaimedCount = $unclaimedSales->count();

        // Previous claims
        $claims = ClaimReimbursement::where('intMerchant_ID', $merchant->intMerchant_ID)
            ->withCount('details')
            ->orderByDesc('dtmSubmittedAt')
            ->get();

        return view('umkm.claims.index', compact('merchant', 'unclaimedAmount', 'unclaimedCount', 'claims'));
    }

    public function store(Request $request)
    {
        $merchant = Auth::user()->merchant;
        if (!$merchant) abort(403);

        $request->validate([
            'bank_name' => 'required|string|max:50',
            'bank_account_number' => 'required|string|max:50',
            'bank_account_holder' => 'required|string|max:150',
            'notes' => 'nullable|string|max:255',
        ]);

        return DB::transaction(function () use ($merchant, $request) {
            $unclaimedSales = Penjualan::where('intMerchant_ID', $merchant->intMerchant_ID)
                ->where('bitIsClaimed', 0)
                ->where('floatVoucherAmount', '>', 0)
                ->lockForUpdate()
                ->get();

            if ($unclaimedSales->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada transaksi voucher yang dapat diklaim saat ini.');
            }

            $totalAmount = (float) $unclaimedSales->sum('floatVoucherAmount');
            $count = $unclaimedSales->count();

            // Update merchant bank info if changed
            $merchant->update([
                'txtBankName' => $request->input('bank_name'),
                'txtBankAccountNumber' => $request->input('bank_account_number'),
                'txtBankAccountHolder' => $request->input('bank_account_holder'),
            ]);

            // Generate claim number
            $seq = ClaimReimbursement::whereDate('dtmSubmittedAt', today())->count() + 1;
            $claimNumber = 'CLM-KMI-' . date('Ymd') . '-' . str_pad($merchant->intMerchant_ID, 2, '0', STR_PAD_LEFT) . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);

            // Create claim
            $claim = ClaimReimbursement::create([
                'txtClaimNumber' => $claimNumber,
                'intMerchant_ID' => $merchant->intMerchant_ID,
                'floatTotalClaimAmount' => $totalAmount,
                'intTotalTransactions' => $count,
                'txtBankName' => $request->input('bank_name'),
                'txtBankAccountNumber' => $request->input('bank_account_number'),
                'txtBankAccountHolder' => $request->input('bank_account_holder'),
                'txtStatus' => 'Diajukan',
                'txtNotes' => $request->input('notes'),
                'dtmSubmittedAt' => now(),
            ]);

            // Link details & mark sales as claimed
            foreach ($unclaimedSales as $sale) {
                ClaimReimbursementDetail::create([
                    'intClaim_ID' => $claim->intClaim_ID,
                    'intPenjualan_ID' => $sale->intPenjualan_ID,
                    'floatVoucherAmount' => $sale->floatVoucherAmount,
                ]);

                $sale->update([
                    'bitIsClaimed' => 1,
                    'intClaim_ID' => $claim->intClaim_ID,
                ]);
            }

            LogActivity::record('SUBMIT_CLAIM', "Pengajuan klaim penggantian voucher {$claimNumber} senilai Rp " . number_format($totalAmount, 0, ',', '.') . " oleh {$merchant->txtMerchantName}.");

            return redirect()->route('umkm.claims.invoice', $claim->intClaim_ID)->with('success', "Klaim berhasil diajukan dengan nomor {$claimNumber}! Silakan cetak atau simpan invoice klaim Anda.");
        });
    }

    public function invoice(int $id)
    {
        $merchant = Auth::user()->merchant;
        $claim = ClaimReimbursement::where('intClaim_ID', $id)
            ->where('intMerchant_ID', $merchant->intMerchant_ID)
            ->with(['merchant', 'details.sale.employee'])
            ->firstOrFail();

        return view('umkm.claims.invoice', compact('claim'));
    }

    public function updateBank(Request $request)
    {
        $merchant = Auth::user()->merchant;
        if (!$merchant) abort(403);

        $validated = $request->validate([
            'bank_name' => 'required|string|max:50',
            'bank_account_number' => 'required|string|max:50',
            'bank_account_holder' => 'required|string|max:150',
        ]);

        $merchant->update([
            'txtBankName' => trim($validated['bank_name']),
            'txtBankAccountNumber' => trim($validated['bank_account_number']),
            'txtBankAccountHolder' => trim($validated['bank_account_holder']),
        ]);

        LogActivity::record('UPDATE_MERCHANT_BANK', "UMKM {$merchant->txtMerchantName} memperbarui rekening pencairan: {$validated['bank_name']} - {$validated['bank_account_number']}.");

        return redirect()->route('umkm.claims.index')->with('success', 'Rekening pencairan dana berhasil diperbarui!');
    }
}

