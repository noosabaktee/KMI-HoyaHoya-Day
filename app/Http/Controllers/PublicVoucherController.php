<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;

class PublicVoucherController extends Controller
{
    public function show(string $code)
    {
        $voucher = Voucher::with('employee')
            ->where('txtVoucherCode', $code)
            ->where('bitActive', 1)
            ->firstOrFail();

        return view('public.voucher', compact('voucher'));
    }
}

