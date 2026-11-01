<?php

namespace App\Http\Controllers\Umkm;

use App\Http\Controllers\Controller;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $merchant = Auth::user()->merchant;
        if (!$merchant) abort(403);

        $paymentType = $request->input('payment_type');
        $query = Penjualan::where('intMerchant_ID', $merchant->intMerchant_ID)
            ->with(['employee', 'details', 'user'])
            ->orderByDesc('dtmInserted');

        if (!empty($paymentType)) {
            $query->where('txtPaymentType', $paymentType);
        }

        $sales = $query->paginate(15)->withQueryString();

        $stats = [
            'total_turnover' => (float) Penjualan::where('intMerchant_ID', $merchant->intMerchant_ID)->sum('floatTotalAmount'),
            'total_voucher' => (float) Penjualan::where('intMerchant_ID', $merchant->intMerchant_ID)->sum('floatVoucherAmount'),
            'total_cash' => (float) Penjualan::where('intMerchant_ID', $merchant->intMerchant_ID)->sum('floatCashAmount'),
            'count' => Penjualan::where('intMerchant_ID', $merchant->intMerchant_ID)->count(),
        ];

        return view('umkm.sales.index', compact('sales', 'stats', 'merchant', 'paymentType'));
    }

    public function show(int $id)
    {
        $merchant = Auth::user()->merchant;
        $sale = Penjualan::where('intPenjualan_ID', $id)
            ->where('intMerchant_ID', $merchant->intMerchant_ID)
            ->with(['employee', 'details', 'voucher', 'user'])
            ->firstOrFail();

        return view('umkm.sales.show', compact('sale', 'merchant'));
    }
}

