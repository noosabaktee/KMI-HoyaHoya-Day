@extends('layouts.app')

@section('header_title', 'Klaim Reimbursement UMKM - Hoya-Hoya Day Festival')

@section('content')
<div class="space-y-6">

  <!-- Header Banner -->
  <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
      <h1 class="text-xl font-extrabold text-slate-900">Persetujuan & Pembayaran Klaim UMKM</h1>
      <p class="text-xs text-slate-500 mt-1">
        Kelola pengajuan pencairan dana dari UMKM atas penjualan produk yang dibeli karyawan menggunakan voucher digital festival.
      </p>
    </div>

    <!-- Quick Stats -->
    <div class="flex items-center space-x-3">
      <div class="px-3 py-2 bg-amber-50 border border-amber-200 rounded-xl text-left">
        <div class="text-[10px] font-bold uppercase tracking-wider text-amber-700">Perlu Diproses</div>
        <div class="text-sm font-extrabold text-amber-900">Rp {{ number_format($stats['total_nominal_diajukan'], 0, ',', '.') }}</div>
      </div>
      <div class="px-3 py-2 bg-emerald-50 border border-emerald-200 rounded-xl text-left">
        <div class="text-[10px] font-bold uppercase tracking-wider text-kmi-700">Telah Dibayar</div>
        <div class="text-sm font-extrabold text-kmi-900">Rp {{ number_format($stats['total_nominal_dibayar'], 0, ',', '.') }}</div>
      </div>
    </div>
  </div>

  <!-- Filter Status Tabs -->
  <div class="flex items-center space-x-2 border-b border-slate-200 pb-3">
    <a href="{{ route('admin.claims.index') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ empty($status) ? 'bg-slate-800 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
      Semua Klaim ({{ $stats['total'] }})
    </a>
    <a href="{{ route('admin.claims.index', ['status' => 'Diajukan']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ $status === 'Diajukan' ? 'bg-amber-500 text-white' : 'text-amber-700 bg-amber-50 hover:bg-amber-100' }}">
      Diajukan ({{ $stats['diajukan'] }})
    </a>
    <a href="{{ route('admin.claims.index', ['status' => 'Disetujui']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ $status === 'Disetujui' ? 'bg-sky-600 text-white' : 'text-sky-700 bg-sky-50 hover:bg-sky-100' }}">
      Disetujui ({{ $stats['disetujui'] }})
    </a>
    <a href="{{ route('admin.claims.index', ['status' => 'Dibayar']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ $status === 'Dibayar' ? 'bg-kmi-600 text-white' : 'text-kmi-700 bg-kmi-50 hover:bg-kmi-100' }}">
      Dibayar ({{ $stats['dibayar'] }})
    </a>
  </div>

  <!-- Claims Table -->
  <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-slate-50/80 text-slate-400 border-b border-slate-100 uppercase tracking-wider text-[10px]">
          <tr>
            <th class="py-3.5 px-4">No Klaim</th>
            <th class="py-3.5 px-4">Booth & Nama UMKM</th>
            <th class="py-3.5 px-4">Tanggal Pengajuan</th>
            <th class="py-3.5 px-4">Total Voucher Diklaim</th>
            <th class="py-3.5 px-4">Rekening Tujuan Transfer</th>
            <th class="py-3.5 px-4">Status</th>
            <th class="py-3.5 px-4 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($claims as $claim)
            <tr class="hover:bg-slate-50/70">
              <td class="py-3.5 px-4 font-mono font-bold text-slate-900">{{ $claim->txtClaimNumber }}</td>
              <td class="py-3.5 px-4">
                <div class="font-bold text-slate-900">{{ $claim->merchant?->txtMerchantName ?? '-' }}</div>
                <div class="text-[10px] text-slate-400">{{ $claim->merchant?->txtBoothNumber }} &bull; {{ $claim->merchant?->txtOwnerName }}</div>
              </td>
              <td class="py-3.5 px-4 text-slate-600">
                {{ $claim->dtmSubmittedAt->format('d/m/Y H:i') }}
              </td>
              <td class="py-3.5 px-4">
                <div class="font-extrabold text-sm text-slate-900">Rp {{ number_format($claim->floatTotalClaimAmount, 0, ',', '.') }}</div>
                <div class="text-[10px] text-slate-400">{{ $claim->intTotalTransactions }} transaksi voucher</div>
              </td>
              <td class="py-3.5 px-4">
                <span class="font-semibold text-slate-800">{{ $claim->txtBankName }}</span>
                <span class="font-mono text-slate-600">{{ $claim->txtBankAccountNumber }}</span>
                <div class="text-[10px] text-slate-400">a.n {{ $claim->txtBankAccountHolder }}</div>
              </td>
              <td class="py-3.5 px-4">
                @if($claim->txtStatus === 'Diajukan')
                  <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full font-bold text-[10px]">Menunggu Approval</span>
                @elseif($claim->txtStatus === 'Disetujui')
                  <span class="px-2.5 py-1 bg-sky-100 text-sky-800 rounded-full font-bold text-[10px]">Disetujui (Siap Bayar)</span>
                @elseif($claim->txtStatus === 'Dibayar')
                  <span class="px-2.5 py-1 bg-emerald-100 text-kmi-800 rounded-full font-bold text-[10px]">Lunas / Dibayarkan</span>
                @else
                  <span class="px-2.5 py-1 bg-rose-100 text-rose-800 rounded-full font-bold text-[10px]">{{ $claim->txtStatus }}</span>
                @endif
              </td>
              <td class="py-3.5 px-4">
                <div class="flex items-center justify-center space-x-1.5">
                  <a href="{{ route('admin.claims.show', $claim->intClaim_ID) }}" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold" title="Detail & Proses">
                    Review
                  </a>
                  <a href="{{ route('admin.claims.invoice', $claim->intClaim_ID) }}" target="_blank" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs" title="Cetak Invoice">
                    <i class='bx bx-printer text-base'></i>
                  </a>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="py-8 text-center text-slate-400">Belum ada pengajuan klaim penggantian dari UMKM.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($claims->hasPages())
      <div class="p-4 border-t border-slate-100">
        {{ $claims->links() }}
      </div>
    @endif
  </div>

</div>
@endsection

