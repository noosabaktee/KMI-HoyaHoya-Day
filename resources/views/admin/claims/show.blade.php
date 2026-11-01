@extends('layouts.app')

@section('header_title', 'Detail Pengajuan Klaim #' . $claim->txtClaimNumber)

@section('content')
<div class="space-y-6 max-w-5xl">

  <div class="flex items-center justify-between">
    <a href="{{ route('admin.claims.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 flex items-center space-x-1">
      <i class='bx bx-arrow-back'></i>
      <span>Kembali ke Daftar Klaim</span>
    </a>

    <div class="flex items-center space-x-2">
      <a href="{{ route('admin.claims.invoice', $claim->intClaim_ID) }}" target="_blank" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-xl text-xs flex items-center space-x-1.5">
        <i class='bx bx-printer text-base'></i>
        <span>Lihat & Cetak Invoice Resmi</span>
      </a>
    </div>
  </div>

  <!-- Claim Summary Card -->
  <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-100">
      <div>
        <div class="flex items-center space-x-2">
          <span class="font-mono text-xs font-bold text-slate-400">NOMOR KLAIM:</span>
          <span class="font-mono text-base font-extrabold text-slate-900">{{ $claim->txtClaimNumber }}</span>
        </div>
        <h1 class="text-xl font-extrabold text-slate-900 mt-1">{{ $claim->merchant->txtMerchantName }}</h1>
        <p class="text-xs text-slate-500">{{ $claim->merchant->txtBoothNumber }} &bull; Pemilik: {{ $claim->merchant->txtOwnerName }} ({{ $claim->merchant->txtPhone }})</p>
      </div>

      <div class="text-right">
        <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Penggantian Voucher</div>
        <div class="text-2xl font-extrabold text-kmi-700 mt-1">Rp {{ number_format($claim->floatTotalClaimAmount, 0, ',', '.') }}</div>
        <div class="mt-1">
          @if($claim->txtStatus === 'Diajukan')
            <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full font-bold text-xs">Menunggu Persetujuan Panitia</span>
          @elseif($claim->txtStatus === 'Disetujui')
            <span class="px-3 py-1 bg-sky-100 text-sky-800 rounded-full font-bold text-xs">Disetujui (Menunggu Transfer)</span>
          @elseif($claim->txtStatus === 'Dibayar')
            <span class="px-3 py-1 bg-emerald-100 text-kmi-800 rounded-full font-bold text-xs">Lunas Ditransfer</span>
          @else
            <span class="px-3 py-1 bg-rose-100 text-rose-800 rounded-full font-bold text-xs">{{ $claim->txtStatus }}</span>
          @endif
        </div>
      </div>
    </div>

    <!-- Bank Details Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 py-6 border-b border-slate-100 bg-slate-50/50 p-4 rounded-2xl my-4">
      <div>
        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Bank Tujuan</span>
        <p class="text-sm font-extrabold text-slate-900 mt-0.5">{{ $claim->txtBankName }}</p>
      </div>
      <div>
        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Nomor Rekening</span>
        <p class="text-sm font-mono font-extrabold text-slate-900 mt-0.5">{{ $claim->txtBankAccountNumber }}</p>
      </div>
      <div>
        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Nama Pemilik Rekening</span>
        <p class="text-sm font-bold text-slate-900 mt-0.5">{{ $claim->txtBankAccountHolder }}</p>
      </div>
    </div>

    <!-- Action Workflow Buttons for Admin -->
    <div class="flex flex-wrap items-center justify-end gap-3 pt-2">
      @if($claim->txtStatus === 'Diajukan')
        <!-- Reject Form -->
        <button type="button" onclick="document.getElementById('rejectModal').classList.remove('hidden')" class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-xl text-xs">
          Tolak Pengajuan
        </button>

        <!-- Approve Form -->
        <form action="{{ route('admin.claims.approve', $claim->intClaim_ID) }}" method="POST" onsubmit="return confirm('Setujui pengajuan klaim ini? Dana siap dibayarkan oleh bagian keuangan KMI.')">
          @csrf
          <button type="submit" class="px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white font-bold rounded-xl text-xs shadow-md shadow-sky-600/30">
            Setujui Pengajuan Klaim
          </button>
        </form>
      @elseif($claim->txtStatus === 'Disetujui')
        <!-- Pay Form -->
        <form action="{{ route('admin.claims.pay', $claim->intClaim_ID) }}" method="POST" onsubmit="return confirm('Tandai bahwa dana klaim telah berhasil ditransfer ke rekening UMKM?')">
          @csrf
          <button type="submit" class="px-6 py-2.5 bg-kmi-600 hover:bg-kmi-700 text-white font-bold rounded-xl text-xs shadow-md shadow-kmi-600/30">
            Tandai Lunas / Telah Ditransfer
          </button>
        </form>
      @endif
    </div>
  </div>

  <!-- Itemized Transaction Breakdown Table -->
  <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
    <h2 class="text-base font-extrabold text-slate-900 mb-1">Rincian Transaksi Voucher yang Diklaim</h2>
    <p class="text-xs text-slate-500 mb-4">Total {{ $claim->details->count() }} transaksi penjualan voucher karyawan yang ditagihkan</p>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-slate-50 text-slate-400 uppercase tracking-wider text-[10px]">
          <tr>
            <th class="py-3 px-3">No Invoice</th>
            <th class="py-3 px-3">Waktu Transaksi</th>
            <th class="py-3 px-3">Karyawan Pembeli</th>
            <th class="py-3 px-3">Produk yang Dibeli</th>
            <th class="py-3 px-3 text-right">Nilai Voucher Terpakai</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @foreach($claim->details as $d)
            @php $sale = $d->sale; @endphp
            <tr class="hover:bg-slate-50/70">
              <td class="py-3 px-3 font-mono font-bold text-slate-900">{{ $sale->txtInvoiceNumber }}</td>
              <td class="py-3 px-3 text-slate-500">{{ $sale->dtmInserted->format('d/m/Y H:i') }}</td>
              <td class="py-3 px-3">
                <span class="font-bold text-slate-800">{{ $sale->employee?->txtEmployeeName ?? '-' }}</span>
                <span class="text-[10px] text-slate-400 block font-mono">{{ $sale->employee?->txtEmployeeNik }}</span>
              </td>
              <td class="py-3 px-3 text-slate-700">
                @foreach($sale->details as $item)
                  <span>{{ $item->intQuantity }}x {{ $item->txtProductName }}</span>@if(!$loop->last), @endif
                @endforeach
              </td>
              <td class="py-3 px-3 text-right font-extrabold text-kmi-700">
                Rp {{ number_format($d->floatVoucherAmount, 0, ',', '.') }}
              </td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr class="bg-slate-50 font-bold text-slate-900">
            <td colspan="4" class="py-3 px-3 text-right">TOTAL DITAGIHKAN:</td>
            <td class="py-3 px-3 text-right font-extrabold text-base text-kmi-700">Rp {{ number_format($claim->floatTotalClaimAmount, 0, ',', '.') }}</td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

</div>

<!-- Modal Reject -->
<div id="rejectModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl relative">
    <h3 class="text-base font-bold text-slate-900 mb-2">Tolak Pengajuan Klaim</h3>
    <form action="{{ route('admin.claims.reject', $claim->intClaim_ID) }}" method="POST" class="space-y-4">
      @csrf
      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Alasan Penolakan *</label>
        <textarea name="reason" rows="3" required placeholder="Tuliskan alasan penolakan agar UMKM dapat memperbaiki data..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs"></textarea>
      </div>
      <div class="flex justify-end space-x-2">
        <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl text-xs">Batal</button>
        <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs">Tolak Sekarang</button>
      </div>
    </form>
  </div>
</div>
@endsection

