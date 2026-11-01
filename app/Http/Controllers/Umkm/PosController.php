<?php

namespace App\Http\Controllers\Umkm;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Penjualan;
use App\Models\Product;
use App\Services\VoucherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PosController extends Controller
{
    public function __construct(
        protected VoucherService $voucherService
    ) {}

    public function index()
    {
        $user = Auth::user();
        $merchant = $user->merchant;

        if (!$merchant) {
            abort(403, 'Akun ini belum ditautkan dengan booth UMKM.');
        }

        $categories = Category::where('intMerchant_ID', $merchant->intMerchant_ID)
            ->where('bitActive', 1)
            ->get();

        $products = Product::where('intMerchant_ID', $merchant->intMerchant_ID)
            ->where('bitActive', 1)
            ->with('category')
            ->orderBy('txtProductName')
            ->get();

        return view('umkm.pos', compact('merchant', 'categories', 'products'));
    }

    /**
     * API to check voucher code / scan barcode
     */
    public function checkVoucher(Request $request)
    {
        $code = $request->input('code');
        if (empty($code)) {
            return response()->json(['valid' => false, 'message' => 'Kode barcode tidak boleh kosong.']);
        }

        $result = $this->voucherService->checkVoucherForPos($code);
        return response()->json($result);
    }

    /**
     * Checkout POS sale
     */
    public function checkout(Request $request)
    {
        if ($request->has('cash_given') && is_string($request->input('cash_given'))) {
            $rawCash = preg_replace('/\D/', '', (string) $request->input('cash_given'));
            $request->merge(['cash_given' => $rawCash !== '' ? (float) $rawCash : 0]);
        }

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'voucher_code' => 'nullable|string',
            'cash_given' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $merchant = $user->merchant;

        if (!$merchant) {
            return response()->json(['success' => false, 'message' => 'Akun UMKM tidak valid.'], 403);
        }

        try {
            $penjualan = $this->voucherService->processSale(
                $merchant,
                $user,
                $validated['items'],
                $validated['voucher_code'] ?? null,
                (float) ($validated['cash_given'] ?? 0),
                $validated['notes'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diproses!',
                'sale' => [
                    'id' => $penjualan->intPenjualan_ID,
                    'invoice_number' => $penjualan->txtInvoiceNumber,
                    'total_amount' => $penjualan->floatTotalAmount,
                    'voucher_amount' => $penjualan->floatVoucherAmount,
                    'cash_amount' => $penjualan->floatCashAmount,
                    'change_amount' => $penjualan->floatChangeAmount,
                    'payment_type' => $penjualan->txtPaymentType,
                    'time' => $penjualan->dtmInserted->format('d/m/Y H:i'),
                    'employee_name' => $penjualan->employee?->txtEmployeeName ?? 'Pelanggan Umum',
                    'voucher_remaining' => $penjualan->voucher ? (float) $penjualan->voucher->floatCurrentBalance : null,
                    'items' => $penjualan->details->map(fn($d) => [
                        'name' => $d->txtProductName,
                        'qty' => $d->intQuantity,
                        'price' => $d->floatPrice,
                        'subtotal' => $d->floatSubtotal,
                    ]),
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * View / print receipt
     */
    public function receipt(int $id)
    {
        $user = Auth::user();
        $sale = Penjualan::with(['merchant', 'employee', 'voucher', 'details', 'user'])
            ->where('intPenjualan_ID', $id)
            ->firstOrFail();

        // If UMKM, ensure it belongs to them
        if ($user->isUmkm() && $sale->intMerchant_ID !== $user->intMerchant_ID) {
            abort(403);
        }

        return view('umkm.receipt', compact('sale'));
    }
}

