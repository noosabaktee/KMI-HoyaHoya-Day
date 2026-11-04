@extends('layouts.app')

@section('header_title', 'Klaim Saldo Voucher ke Perusahaan - ' . ($merchant->txtMerchantName ?? 'UMKM'))

@section('content')
<div class="space-y-6 w-full">

  <!-- Header Banner -->
  <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
      <h1 class="text-xl font-extrabold text-slate-900">Pencairan Dana Voucher Festival</h1>
      <p class="text-xs text-slate-500 mt-1">
        Klaim penggantian uang dari PT Kalbe Morinaga Indonesia atas transaksi karyawan yang membayar dengan voucher digital.
      </p>
    </div>

    <div class="px-3 py-1.5 bg-emerald-50 text-kmi-700 border border-emerald-200 rounded-xl text-xs font-bold">
      Stand: {{ $merchant->txtBoothNumber }}
    </div>
  </div>

  <!-- Unclaimed Balance Action Card -->
  <div class="bg-gradient-to-r from-emerald-800 via-kmi-700 to-kmi-800 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-kmi-700/20">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div>
        <span class="text-xs font-bold uppercase tracking-wider text-emerald-200">Saldo Voucher yang Belum Diklaim</span>
        <div class="text-3xl sm:text-4xl font-extrabold mt-1">Rp {{ number_format($unclaimedAmount, 0, ',', '.') }}</div>
        <p class="text-xs text-emerald-100 mt-1.5">
          Terdapat <span class="font-bold text-white">{{ $unclaimedCount }}</span> transaksi voucher yang siap Anda tagihkan ke perusahaan.
        </p>
      </div>

      <div>
        @if($unclaimedAmount > 0)
          <button type="button" onclick="openModal('modalSubmitClaim')" class="px-6 py-3.5 bg-white hover:bg-emerald-50 text-kmi-800 font-extrabold rounded-2xl text-xs uppercase tracking-wider shadow-lg transition-all flex items-center space-x-2">
            <i class='bx bx-send text-lg'></i>
            <span>Ajukan Klaim Sekarang</span>
          </button>
        @else
          <button type="button" disabled class="px-6 py-3.5 bg-white/20 text-emerald-200 font-bold rounded-2xl text-xs cursor-not-allowed">
            Tidak Ada Saldo yang Dapat Diklaim
          </button>
        @endif
      </div>
    </div>
  </div>

  <!-- Bank Account Info Card for UMKM -->
  <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div class="flex items-start sm:items-center space-x-3.5">
      <div class="w-12 h-12 rounded-2xl {{ !empty($merchant->txtBankAccountNumber) ? 'bg-emerald-50 text-kmi-600' : 'bg-amber-50 text-amber-600' }} flex items-center justify-center text-2xl shrink-0">
        <i class='bx {{ !empty($merchant->txtBankAccountNumber) ? 'bxs-bank' : 'bx-error-circle' }}'></i>
      </div>
      <div>
        <div class="flex items-center space-x-2">
          <h2 class="text-sm font-bold text-slate-900">Rekening Tujuan Pencairan Dana</h2>
          @if(!empty($merchant->txtBankAccountNumber))
            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-kmi-800">Tersimpan</span>
          @else
            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800">Belum Diatur</span>
          @endif
        </div>
        @if(!empty($merchant->txtBankAccountNumber))
          <p class="text-xs text-slate-600 mt-0.5">
            Bank <span class="font-bold text-slate-800">{{ $merchant->txtBankName }}</span> &bull; 
            No. Rekening <span class="font-mono font-bold text-slate-900">{{ $merchant->txtBankAccountNumber }}</span> a.n. 
            <span class="font-semibold text-slate-800">{{ $merchant->txtBankAccountHolder }}</span>
          </p>
        @else
          <p class="text-xs text-slate-500 mt-0.5">
            Anda belum mengisi rekening bank. Silakan atur rekening tujuan agar panitia KMI dapat mentransfer pencairan dana voucher Anda.
          </p>
        @endif
      </div>
    </div>

    <button type="button" onclick="openModal('modalUpdateBank')"
      class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-xl text-xs flex items-center justify-center space-x-1.5 transition-colors shrink-0">
      <i class='bx bx-edit text-base'></i>
      <span>{{ !empty($merchant->txtBankAccountNumber) ? 'Ubah Rekening Bank' : 'Input Rekening Sekarang' }}</span>
    </button>
  </div>

  <!-- History of Submitted Claims -->
  <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
    <h2 class="text-base font-extrabold text-slate-900 mb-1">Riwayat Pengajuan Klaim</h2>
    <p class="text-xs text-slate-500 mb-4">Pantau status pencairan dana dan cetak invoice klaim resmi untuk perusahaan</p>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-slate-50/80 text-slate-400 uppercase tracking-wider text-[10px]">
          <tr>
            <th class="py-3 px-3">No Klaim</th>
            <th class="py-3 px-3">Tanggal Pengajuan</th>
            <th class="py-3 px-3">Jumlah Transaksi</th>
            <th class="py-3 px-3">Rekening Pencairan</th>
            <th class="py-3 px-3 text-right">Total Klaim (Rp)</th>
            <th class="py-3 px-3 text-center">Status</th>
            <th class="py-3 px-3 text-center">Invoice</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($claims as $c)
            <tr class="hover:bg-slate-50/70">
              <td class="py-3.5 px-3 font-mono font-bold text-slate-900">{{ $c->txtClaimNumber }}</td>
              <td class="py-3.5 px-3 text-slate-500">{{ $c->dtmSubmittedAt->format('d/m/Y H:i') }}</td>
              <td class="py-3.5 px-3 font-semibold text-slate-700">{{ $c->intTotalTransactions }} transaksi</td>
              <td class="py-3.5 px-3">
                <span class="font-bold text-slate-800">{{ $c->txtBankName }} {{ $c->txtBankAccountNumber }}</span>
                <span class="text-[10px] text-slate-400 block">a.n {{ $c->txtBankAccountHolder }}</span>
              </td>
              <td class="py-3.5 px-3 text-right font-extrabold text-kmi-700 text-sm">
                Rp {{ number_format($c->floatTotalClaimAmount, 0, ',', '.') }}
              </td>
              <td class="py-3.5 px-3 text-center">
                @if($c->txtStatus === 'Diajukan')
                  <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full font-bold text-[10px]">Diajukan (Menunggu Review)</span>
                @elseif($c->txtStatus === 'Disetujui')
                  <span class="px-2.5 py-1 bg-sky-100 text-sky-800 rounded-full font-bold text-[10px]">Disetujui Panitia</span>
                @elseif($c->txtStatus === 'Dibayar')
                  <span class="px-2.5 py-1 bg-emerald-100 text-kmi-800 rounded-full font-bold text-[10px]">Sudah Ditransfer</span>
                @else
                  <span class="px-2.5 py-1 bg-rose-100 text-rose-800 rounded-full font-bold text-[10px]">{{ $c->txtStatus }}</span>
                @endif
              </td>
              <td class="py-3.5 px-3 text-center">
                <a href="{{ route('umkm.claims.invoice', $c->intClaim_ID) }}" target="_blank" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl font-bold text-xs inline-flex items-center space-x-1" title="Cetak Invoice">
                  <i class='bx bx-printer text-sm'></i>
                  <span>Invoice</span>
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="py-8 text-center text-slate-400">Belum ada pengajuan klaim sebelumnya.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>

<!-- Modal Submit Claim -->
<div id="modalSubmitClaim" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative">
    <button type="button" onclick="closeModal('modalSubmitClaim')" class="absolute top-5 right-5 text-slate-400 hover:text-slate-700">
      <i class='bx bx-x text-2xl'></i>
    </button>

    <h3 class="text-base font-extrabold text-slate-900 mb-1">Konfirmasi Pengajuan Klaim</h3>
    <p class="text-xs text-slate-500 mb-4">Pastikan data rekening bank Anda sudah benar</p>

    <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-100 mb-4 text-xs">
      <div class="flex justify-between font-bold text-emerald-950">
        <span>Total yang Diklaim:</span>
        <span class="text-sm font-extrabold text-kmi-700">Rp {{ number_format($unclaimedAmount, 0, ',', '.') }}</span>
      </div>
      <div class="text-[11px] text-emerald-800 mt-1">Mencakup {{ $unclaimedCount }} transaksi voucher karyawan</div>
    </div>

    <form action="{{ route('umkm.claims.store') }}" method="POST" class="space-y-3">
      @csrf

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Bank *</label>
        <input type="text" name="bank_name" required value="{{ $merchant->txtBankName ?? 'BCA' }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Rekening *</label>
        <input type="text" name="bank_account_number" required value="{{ $merchant->txtBankAccountNumber }}" placeholder="Contoh: 1234567890" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono font-bold">
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Pemilik Rekening *</label>
        <input type="text" name="bank_account_holder" required value="{{ $merchant->txtBankAccountHolder ?? $merchant->txtOwnerName }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
        <textarea name="notes" rows="2" placeholder="Catatan untuk panitia..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs"></textarea>
      </div>

      <div class="pt-2 flex justify-end space-x-2">
        <button type="button" onclick="closeModal('modalSubmitClaim')" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl text-xs">Batal</button>
        <button type="submit" class="px-5 py-2 bg-kmi-600 hover:bg-kmi-700 text-white font-bold rounded-xl text-xs shadow-md shadow-kmi-600/30">Ajukan Sekarang</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Update Bank -->
<div id="modalUpdateBank" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl relative">
    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
      <div>
        <h3 class="text-base font-extrabold text-slate-900">Pengaturan Rekening Bank</h3>
        <p class="text-xs text-slate-500">Rekening tujuan transfer dana klaim voucher dari panitia KMI</p>
      </div>
      <button type="button" onclick="closeModal('modalUpdateBank')" class="p-1 text-slate-400 hover:text-slate-700">
        <i class='bx bx-x text-2xl'></i>
      </button>
    </div>

    <form action="{{ route('umkm.claims.bank') }}" method="POST" class="space-y-3.5">
      @csrf
      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Bank *</label>
        <input type="text" name="bank_name" required value="{{ $merchant->txtBankName }}" placeholder="Contoh: BCA, Mandiri, BRI, BNI" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-kmi-600 focus:bg-white">
      </div>
      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Rekening *</label>
        <input type="text" name="bank_account_number" required value="{{ $merchant->txtBankAccountNumber }}" placeholder="Contoh: 8830123456" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono font-bold focus:outline-none focus:border-kmi-600 focus:bg-white">
      </div>
      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Pemilik Rekening *</label>
        <input type="text" name="bank_account_holder" required value="{{ $merchant->txtBankAccountHolder ?? $merchant->txtOwnerName }}" placeholder="Nama sesuai buku tabungan" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-kmi-600 focus:bg-white">
      </div>

      <div class="pt-2 flex justify-end space-x-2">
        <button type="button" onclick="closeModal('modalUpdateBank')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs">Batal</button>
        <button type="submit" class="px-5 py-2 bg-kmi-600 hover:bg-kmi-700 text-white font-bold rounded-xl text-xs shadow-md shadow-kmi-600/30">Simpan Rekening</button>
      </div>
    </form>
  </div>
</div>

<script>
  function openModal(id) { document.getElementById(id)?.classList.remove('hidden'); }
  function closeModal(id) { document.getElementById(id)?.classList.add('hidden'); }
</script>
@endsection

