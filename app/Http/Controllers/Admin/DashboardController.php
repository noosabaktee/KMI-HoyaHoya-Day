<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClaimReimbursement;
use App\Models\Employee;
use App\Models\Merchant;
use App\Models\Penjualan;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Economic Turnover Metrics
        $totalVoucherUsed = (float) Penjualan::sum('floatVoucherAmount');
        $totalCash = (float) Penjualan::sum('floatCashAmount');
        $totalEconomicTurnover = (float) Penjualan::sum('floatTotalAmount');
        $totalTransactions = Penjualan::count();

        // 2. Voucher & Employee Stats
        $totalEmployees = Employee::count();
        $totalVouchers = Voucher::count();
        $totalDistributedAmount = (float) Voucher::sum('floatInitialBalance');
        $remainingVoucherBalance = (float) Voucher::sum('floatCurrentBalance');

        $voucherSentCount = Voucher::where('txtStatus', 'Terkirim')->count();
        $voucherActiveCount = Voucher::where('txtStatus', 'Aktif')->count();
        $voucherFinishedCount = Voucher::where('txtStatus', 'Habis')->count();

        // 3. Merchant & Claim Stats
        $totalMerchants = Merchant::where('bitActive', 1)->count();
        $totalClaimSubmitted = (float) ClaimReimbursement::where('txtStatus', 'Diajukan')->sum('floatTotalClaimAmount');
        $totalClaimApproved = (float) ClaimReimbursement::where('txtStatus', 'Disetujui')->sum('floatTotalClaimAmount');
        $totalClaimPaid = (float) ClaimReimbursement::where('txtStatus', 'Dibayar')->sum('floatTotalClaimAmount');

        // 4. Performance per UMKM Booth
        $merchantPerformances = Merchant::where('bitActive', 1)
            ->withCount('sales')
            ->withSum('sales as total_revenue', 'floatTotalAmount')
            ->withSum('sales as total_voucher', 'floatVoucherAmount')
            ->withSum('sales as total_cash', 'floatCashAmount')
            ->orderByDesc('total_revenue')
            ->get();

        // 5. Recent Transactions
        $recentTransactions = Penjualan::with(['merchant', 'employee', 'user'])
            ->orderByDesc('dtmInserted')
            ->limit(10)
            ->get();

        // 6. Chart: Sales per hour (database agnostic between PostgreSQL and SQLite)
        $isPgsql = DB::getDriverName() === 'pgsql';
        $hourExpr = $isPgsql ? 'EXTRACT(HOUR FROM "dtmInserted")' : "strftime('%H', \"dtmInserted\")";

        $salesByHour = Penjualan::select(
            DB::raw("{$hourExpr} as hour"),
            DB::raw("SUM(\"floatTotalAmount\") as total"),
            DB::raw("SUM(\"floatVoucherAmount\") as voucher"),
            DB::raw("SUM(\"floatCashAmount\") as cash")
        )
        ->groupBy('hour')
        ->orderBy('hour')
        ->get();

        return view('admin.dashboard', compact(
            'totalVoucherUsed',
            'totalCash',
            'totalEconomicTurnover',
            'totalTransactions',
            'totalEmployees',
            'totalVouchers',
            'totalDistributedAmount',
            'remainingVoucherBalance',
            'voucherSentCount',
            'voucherActiveCount',
            'voucherFinishedCount',
            'totalMerchants',
            'totalClaimSubmitted',
            'totalClaimApproved',
            'totalClaimPaid',
            'merchantPerformances',
            'recentTransactions',
            'salesByHour'
        ));
    }
}
