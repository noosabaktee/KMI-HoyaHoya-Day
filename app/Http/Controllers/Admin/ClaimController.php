<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClaimReimbursement;
use App\Models\LogActivity;
use Illuminate\Http\Request;

class ClaimController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $query = ClaimReimbursement::with(['merchant', 'sales'])
            ->orderByDesc('dtmSubmittedAt');

        if (!empty($status)) {
            $query->where('txtStatus', $status);
        }

        $claims = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => ClaimReimbursement::count(),
            'diajukan' => ClaimReimbursement::where('txtStatus', 'Diajukan')->count(),
            'disetujui' => ClaimReimbursement::where('txtStatus', 'Disetujui')->count(),
            'dibayar' => ClaimReimbursement::where('txtStatus', 'Dibayar')->count(),
            'total_nominal_diajukan' => (float) ClaimReimbursement::where('txtStatus', 'Diajukan')->sum('floatTotalClaimAmount'),
            'total_nominal_dibayar' => (float) ClaimReimbursement::where('txtStatus', 'Dibayar')->sum('floatTotalClaimAmount'),
        ];

        return view('admin.claims.index', compact('claims', 'stats', 'status'));
    }

    public function show(int $id)
    {
        $claim = ClaimReimbursement::with(['merchant', 'details.sale.employee', 'details.sale.details'])
            ->findOrFail($id);

        return view('admin.claims.show', compact('claim'));
    }

    public function approve(int $id)
    {
        $claim = ClaimReimbursement::findOrFail($id);
        if ($claim->txtStatus !== 'Diajukan') {
            return redirect()->back()->with('error', 'Klaim ini sudah diproses sebelumnya.');
        }

        $claim->update([
            'txtStatus' => 'Disetujui',
            'dtmApprovedAt' => now(),
            'txtUpdatedBy' => auth()->user()->txtUsername ?? 'ADMIN',
        ]);

        LogActivity::record('APPROVE_CLAIM', "Klaim {$claim->txtClaimNumber} disetujui untuk {$claim->merchant->txtMerchantName}.");

        return redirect()->back()->with('success', "Klaim {$claim->txtClaimNumber} berhasil disetujui!");
    }

    public function markAsPaid(Request $request, int $id)
    {
        $claim = ClaimReimbursement::findOrFail($id);

        $claim->update([
            'txtStatus' => 'Dibayar',
            'dtmPaidAt' => now(),
            'txtNotes' => $request->input('notes', $claim->txtNotes),
            'txtUpdatedBy' => auth()->user()->txtUsername ?? 'ADMIN',
        ]);

        LogActivity::record('PAY_CLAIM', "Klaim {$claim->txtClaimNumber} telah ditransfer/dibayar ke {$claim->merchant->txtMerchantName}.");

        return redirect()->back()->with('success', "Klaim {$claim->txtClaimNumber} telah ditandai lunas / dibayarkan.");
    }

    public function reject(Request $request, int $id)
    {
        $request->validate(['reason' => 'required|string|max:500']);
        $claim = ClaimReimbursement::findOrFail($id);

        $claim->update([
            'txtStatus' => 'Ditolak',
            'txtRejectionReason' => $request->input('reason'),
            'txtUpdatedBy' => auth()->user()->txtUsername ?? 'ADMIN',
        ]);

        // Release sales so merchant can claim again
        $claim->sales()->update(['bitIsClaimed' => 0, 'intClaim_ID' => null]);

        LogActivity::record('REJECT_CLAIM', "Klaim {$claim->txtClaimNumber} ditolak: {$request->input('reason')}.");

        return redirect()->back()->with('warning', "Klaim {$claim->txtClaimNumber} telah ditolak.");
    }

    public function printInvoice(int $id)
    {
        $claim = ClaimReimbursement::with(['merchant', 'details.sale.employee'])
            ->findOrFail($id);

        return view('admin.claims.invoice', compact('claim'));
    }
}

