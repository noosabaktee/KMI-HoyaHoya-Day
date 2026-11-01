@extends('layouts.app')

@section('header_title', 'Riwayat Penjualan - ' . ($merchant->txtMerchantName ?? 'UMKM'))

@section('content')
<div class="space-y-6">

  <!-- Header Banner -->
  <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-xl font-extrabold text-slate-900">Riwayat Penjualan Stand {{ $merchant->txtBoothNumber }}</h1>
      <p class="text-xs text-slate-500 mt-1">
        Daftar seluruh transaksi yang terjadi di booth {{ $merchant->txtMerchantName }}.
      </p>
    </div>

    <div class="flex items-center space-x-2">
      <a href="{{ route('umkm.claims.index') }}" class="px-4 py-2.5 bg-kmi-600 hover:bg-kmi-700 text-white font-bold rounded-xl text-xs shadow-md shadow-kmi-600/30 flex items-center space-x-1.5">
        <i class='bx bx-wallet text-base'></i>
        <span>Klaim Saldo Voucher &rarr;</span>
      </a>
    </div>
  </div>

  <!-- Summary Cards -->
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
      <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Transaksi</span>
      <p class="text-xl font-extrabold text-slate-900 mt-1">{{ $stats['count'] }}</p>
    </div>
    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
      <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Pendapatan</span>
      <p class="text-xl font-extrabold text-slate-900 mt-1">Rp {{ number_format($stats['total_turnover'], 0, ',', '.') }}</p>
    </div>
    <div class="bg-white p-4 rounded-2xl border border-sky-100 bg-sky-50/20 shadow-sm">
      <span class="text-[10px] font-bold uppercase tracking-wider text-sky-700">Omzet Voucher</span>
      <p class="text-xl font-extrabold text-sky-800 mt-1">Rp {{ number_format($stats['total_voucher'], 0, ',', '.') }}</p>
    </div>
    <div class="bg-white p-4 rounded-2xl border border-amber-100 bg-amber-50/20 shadow-sm">
      <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700">Omzet Uang Tunai</span>
      <p class="text-xl font-extrabold text-amber-800 mt-1">Rp {{ number_format($stats['total_cash'], 0, ',', '.') }}</p>
    </div>
  </div>

  <!-- Filter Pills -->
  <div class="flex items-center space-x-2 border-b border-slate-200 pb-3">
    <a href="{{ route('umkm.sales.index') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ empty($paymentType) ? 'bg-slate-800 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
      Semua Penjualan
    </a>
    <a href="{{ route('umkm.sales.index', ['payment_type' => 'VOUCHER']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ $paymentType === 'VOUCHER' ? 'bg-sky-600 text-white' : 'text-sky-700 bg-sky-50 hover:bg-sky-100' }}">
      Voucher
    </a>
    <a href="{{ route('umkm.sales.index', ['payment_type' => 'SPLIT']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ $paymentType === 'SPLIT' ? 'bg-purple-600 text-white' : 'text-purple-700 bg-purple-50 hover:bg-purple-100' }}">
      Voucher + Tunai
    </a>
    <a href="{{ route('umkm.sales.index', ['payment_type' => 'CASH']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ $paymentType === 'CASH' ? 'bg-amber-600 text-white' : 'text-amber-700 bg-amber-50 hover:bg-amber-100' }}">
      Tunai Murni
    </a>
  </div>

  <!-- Sales List Table -->
  <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-slate-50/80 text-slate-400 border-b border-slate-100 uppercase tracking-wider text-[10px]">
          <tr>
            <th class="py-3.5 px-4">No Invoice</th>
            <th class="py-3.5 px-4">Waktu</th>
            <th class="py-3.5 px-4">Pelanggan</th>
            <th class="py-3.5 px-4">Menu yang Dipesan</th>
            <th class="py-3.5 px-4">Metode Bayar</th>
            <th class="py-3.5 px-4 text-right">Total (Rp)</th>
            <th class="py-3.5 px-4 text-center">Status Klaim</th>
            <th class="py-3.5 px-4 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($sales as $sale)
            <tr class="hover:bg-slate-50/70">
              <td class="py-3.5 px-4 font-mono font-bold text-slate-900">{{ $sale->txtInvoiceNumber }}</td>
              <td class="py-3.5 px-4 text-slate-500">{{ $sale->dtmInserted->format('d/m/Y H:i') }}</td>
              <td class="py-3.5 px-4 font-medium text-slate-800">
                {{ $sale->employee?->txtEmployeeName ?? 'Pelanggan Umum' }}
              </td>
              <td class="py-3.5 px-4 text-slate-700">
                @foreach($sale->details as $d)
                  <span>{{ $d->intQuantity }}x {{ $d->txtProductName }}</span>@if(!$loop->last), @endif
                @endforeach
              </td>
              <td class="py-3.5 px-4">
                @if($sale->txtPaymentType === 'VOUCHER')
                  <span class="px-2 py-0.5 bg-sky-50 text-sky-700 rounded-md font-semibold text-[10px]">Voucher</span>
                @elseif($sale->txtPaymentType === 'SPLIT')
                  <span class="px-2 py-0.5 bg-purple-50 text-purple-700 rounded-md font-semibold text-[10px]">Voucher + Cash</span>
                @else
                  <span class="px-2 py-0.5 bg-amber-50 text-amber-700 rounded-md font-semibold text-[10px]">Tunai</span>
                @endif
              </td>
              <td class="py-3.5 px-4 text-right font-extrabold text-slate-900">
                Rp {{ number_format($sale->floatTotalAmount, 0, ',', '.') }}
              </td>
              <td class="py-3.5 px-4 text-center">
                @if($sale->floatVoucherAmount <= 0)
                  <span class="text-[10px] text-slate-400">-</span>
                @elseif($sale->bitIsClaimed)
                  <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded-md font-semibold text-[10px]">Sudah Diklaim</span>
                @else
                  <span class="px-2 py-0.5 bg-amber-50 text-amber-700 rounded-md font-semibold text-[10px]">Belum Diklaim</span>
                @endif
              </td>
              <td class="py-3.5 px-4 text-center">
                <a href="{{ route('umkm.pos.receipt', $sale->intPenjualan_ID) }}" target="_blank" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs inline-block" title="Cetak Struk">
                  <i class='bx bx-printer text-base'></i>
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="py-8 text-center text-slate-400">Belum ada transaksi di booth Anda.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($sales->hasPages())
      <div class="p-4 border-t border-slate-100">
        {{ $sales->links() }}
      </div>
    @endif
  </div>

</div>
@endsection

