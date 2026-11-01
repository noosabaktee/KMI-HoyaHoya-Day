@extends('layouts.app')

@section('header_title', 'Laporan Transaksi & Perputaran Ekonomi - Hoya-Hoya Festival')

@section('content')
<div class="space-y-6">

  <!-- Header Banner -->
  <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
      <h1 class="text-xl font-extrabold text-slate-900">Rekapitulasi Penjualan & Dampak Ekonomi UMKM</h1>
      <p class="text-xs text-slate-500 mt-1">
        Laporan terperinci transaksi voucher belanja karyawan dan transaksi tunai sepanjang Hoya-Hoya Day Festival.
      </p>
    </div>

    <!-- Export Button -->
    <a href="{{ route('admin.reports.export', request()->query()) }}" class="px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-xl text-xs shadow-md shadow-emerald-700/20 flex items-center space-x-2">
      <i class='bx bx-spreadsheet text-base'></i>
      <span>Export ke Excel (.xlsx)</span>
    </a>
  </div>

  <!-- Summary 4-Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
      <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Transaksi</span>
      <p class="text-xl font-extrabold text-slate-900 mt-1">{{ $summary['total_count'] }}</p>
    </div>
    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
      <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Perputaran Uang</span>
      <p class="text-xl font-extrabold text-slate-900 mt-1">Rp {{ number_format($summary['total_sales'], 0, ',', '.') }}</p>
    </div>
    <div class="bg-white p-4 rounded-2xl border border-sky-100 bg-sky-50/20 shadow-sm">
      <span class="text-[10px] font-bold uppercase tracking-wider text-sky-600">Total Dibayar Voucher</span>
      <p class="text-xl font-extrabold text-sky-800 mt-1">Rp {{ number_format($summary['total_voucher'], 0, ',', '.') }}</p>
    </div>
    <div class="bg-white p-4 rounded-2xl border border-amber-100 bg-amber-50/20 shadow-sm">
      <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600">Total Dibayar Tunai (Cash)</span>
      <p class="text-xl font-extrabold text-amber-800 mt-1">Rp {{ number_format($summary['total_cash'], 0, ',', '.') }}</p>
    </div>
  </div>

  <!-- Filter Controls -->
  <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm">
    <form action="{{ route('admin.reports.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
      <div>
        <label class="block text-[11px] font-bold text-slate-600 mb-1">Pilih Booth UMKM</label>
        <select name="merchant_id" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700">
          <option value="">Semua Booth UMKM</option>
          @foreach($merchants as $m)
            <option value="{{ $m->intMerchant_ID }}" {{ $merchantId == $m->intMerchant_ID ? 'selected' : '' }}>
              {{ $m->txtBoothNumber }} - {{ $m->txtMerchantName }}
            </option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-[11px] font-bold text-slate-600 mb-1">Metode Pembayaran</label>
        <select name="payment_type" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700">
          <option value="">Semua Metode</option>
          <option value="VOUCHER" {{ $paymentType === 'VOUCHER' ? 'selected' : '' }}>Voucher Penuh</option>
          <option value="SPLIT" {{ $paymentType === 'SPLIT' ? 'selected' : '' }}>Split (Voucher + Cash)</option>
          <option value="CASH" {{ $paymentType === 'CASH' ? 'selected' : '' }}>Tunai / Cash</option>
        </select>
      </div>

      <div>
        <label class="block text-[11px] font-bold text-slate-600 mb-1">Tanggal Mulai</label>
        <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700">
      </div>

      <div class="flex items-end space-x-2">
        <button type="submit" class="flex-1 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl text-xs">
          Terapkan Filter
        </button>
        <a href="{{ route('admin.reports.index') }}" class="p-2 text-slate-400 hover:text-rose-600 text-xs">Reset</a>
      </div>
    </form>
  </div>

  <!-- Sales Transactions Table -->
  <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-slate-50/80 text-slate-400 border-b border-slate-100 uppercase tracking-wider text-[10px]">
          <tr>
            <th class="py-3 px-4">No Invoice</th>
            <th class="py-3 px-4">Waktu</th>
            <th class="py-3 px-4">Booth UMKM</th>
            <th class="py-3 px-4">Karyawan Pembeli</th>
            <th class="py-3 px-4">Metode</th>
            <th class="py-3 px-4 text-right">Total Transaksi</th>
            <th class="py-3 px-4 text-right">Dibayar Voucher</th>
            <th class="py-3 px-4 text-right">Dibayar Cash</th>
            <th class="py-3 px-4 text-center">Status Klaim</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($sales as $sale)
            <tr class="hover:bg-slate-50/70">
              <td class="py-3 px-4 font-mono font-bold text-slate-900">{{ $sale->txtInvoiceNumber }}</td>
              <td class="py-3 px-4 text-slate-500">{{ $sale->dtmInserted->format('d/m/Y H:i') }}</td>
              <td class="py-3 px-4 font-medium text-slate-800">{{ $sale->merchant?->txtMerchantName ?? '-' }}</td>
              <td class="py-3 px-4">
                <span class="font-bold text-slate-800">{{ $sale->employee?->txtEmployeeName ?? 'Pelanggan Umum' }}</span>
                @if($sale->employee)
                  <span class="text-[10px] text-slate-400 font-mono block">{{ $sale->employee->txtEmployeeNik }}</span>
                @endif
              </td>
              <td class="py-3 px-4">
                @if($sale->txtPaymentType === 'VOUCHER')
                  <span class="px-2 py-0.5 bg-sky-50 text-sky-700 rounded-md font-semibold text-[10px]">Voucher</span>
                @elseif($sale->txtPaymentType === 'SPLIT')
                  <span class="px-2 py-0.5 bg-purple-50 text-purple-700 rounded-md font-semibold text-[10px]">Voucher + Cash</span>
                @else
                  <span class="px-2 py-0.5 bg-amber-50 text-amber-700 rounded-md font-semibold text-[10px]">Tunai</span>
                @endif
              </td>
              <td class="py-3 px-4 text-right font-extrabold text-slate-900">
                Rp {{ number_format($sale->floatTotalAmount, 0, ',', '.') }}
              </td>
              <td class="py-3 px-4 text-right font-bold text-sky-800">
                Rp {{ number_format($sale->floatVoucherAmount, 0, ',', '.') }}
              </td>
              <td class="py-3 px-4 text-right font-bold text-amber-800">
                Rp {{ number_format($sale->floatCashAmount, 0, ',', '.') }}
              </td>
              <td class="py-3 px-4 text-center">
                @if($sale->floatVoucherAmount <= 0)
                  <span class="text-[10px] text-slate-400">-</span>
                @elseif($sale->bitIsClaimed)
                  <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded-md font-semibold text-[10px]">Sudah Diklaim</span>
                @else
                  <span class="px-2 py-0.5 bg-slate-100 text-slate-600 rounded-md font-semibold text-[10px]">Belum Diklaim</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="py-8 text-center text-slate-400">Tidak ada data transaksi yang sesuai filter.</td>
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

