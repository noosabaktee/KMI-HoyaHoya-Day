<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Invoice Klaim {{ $claim->txtClaimNumber }} - PT Kalbe Morinaga Indonesia</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f1f5f9; }
    @media print {
      body { background: white !important; padding: 0 !important; }
      .no-print { display: none !important; }
      .print-shadow { box-shadow: none !important; border: none !important; }
    }
  </style>
</head>
<body class="p-4 sm:p-8 flex justify-center">

  <div class="max-w-4xl w-full">
    <!-- Action Bar (Hidden on print) -->
    <div class="no-print mb-4 flex items-center justify-between">
      <button onclick="window.history.back()" class="px-4 py-2 bg-white text-slate-700 font-bold rounded-xl text-xs shadow-sm hover:bg-slate-50 border border-slate-200">
        &larr; Kembali
      </button>
      <button onclick="window.print()" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-xl text-xs shadow-md flex items-center space-x-1.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
        <span>Cetak Invoice (PDF)</span>
      </button>
    </div>

    <!-- Official Paper Invoice Container -->
    <div class="bg-white rounded-2xl p-8 sm:p-12 shadow-xl border border-slate-200 print-shadow text-slate-800">
      
      <!-- Company Official Header -->
      <div class="flex items-start justify-between pb-6 border-b-2 border-emerald-700">
        <div>
          <div class="flex items-center space-x-3 mb-2">
            <div class="w-12 h-12 rounded-xl bg-emerald-700 text-white font-extrabold text-xl flex items-center justify-center">
              KMI
            </div>
            <div>
              <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">PT KALBE MORINAGA INDONESIA</h1>
              <p class="text-xs font-semibold text-emerald-800">HOYA-HOYA DAY FESTIVAL 2026 &bull; PROGRAM PEMBERDAYAAN UMKM</p>
            </div>
          </div>
          <p class="text-[11px] text-slate-500 max-w-md leading-relaxed">
            Kawasan Industri Indotaisei Sektor 1A Blok Q1, Kalihurip, Cikampek, Karawang, Jawa Barat 41373
          </p>
        </div>

        <div class="text-right">
          <span class="inline-block px-3 py-1 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-xs font-extrabold tracking-wider uppercase mb-1">
            INVOICE KLAIM
          </span>
          <p class="text-sm font-mono font-bold text-slate-900">{{ $claim->txtClaimNumber }}</p>
          <p class="text-xs text-slate-500">Tanggal: {{ $claim->dtmSubmittedAt->format('d F Y') }}</p>
          <div class="mt-2">
            <span class="px-2.5 py-0.5 rounded text-[11px] font-bold uppercase
              {{ $claim->txtStatus === 'Dibayar' ? 'bg-emerald-100 text-emerald-800' : ($claim->txtStatus === 'Disetujui' ? 'bg-sky-100 text-sky-800' : 'bg-amber-100 text-amber-800') }}">
              STATUS: {{ $claim->txtStatus }}
            </span>
          </div>
        </div>
      </div>

      <!-- Parties Information -->
      <div class="grid grid-cols-2 gap-8 py-6 border-b border-slate-200 text-xs">
        <div>
          <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Tagihan Ditujukan Kepada:</h2>
          <p class="font-bold text-slate-900 text-sm">Panitia Hoya-Hoya Day Festival</p>
          <p class="text-slate-600 mt-0.5">Departemen HR & General Affairs / Finance</p>
          <p class="text-slate-600">PT Kalbe Morinaga Indonesia</p>
          <p class="text-slate-500">festival@kmi.kalbe.co.id</p>
        </div>

        <div>
          <h2 class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Diajukan Oleh (Mitra UMKM):</h2>
          <p class="font-bold text-slate-900 text-sm">{{ $claim->merchant->txtMerchantName }}</p>
          <p class="text-slate-600 mt-0.5">Lokasi Stand: <span class="font-semibold text-slate-800">{{ $claim->merchant->txtBoothNumber }}</span></p>
          <p class="text-slate-600">Pemilik: {{ $claim->merchant->txtOwnerName }} ({{ $claim->merchant->txtPhone }})</p>
          
          <div class="mt-2.5 p-2.5 bg-slate-50 border border-slate-200 rounded-lg">
            <p class="text-[10px] font-bold uppercase text-slate-500">Instruksi Rekening Pembayaran:</p>
            <p class="font-extrabold text-slate-900">{{ $claim->txtBankName }} - {{ $claim->txtBankAccountNumber }}</p>
            <p class="text-slate-600">a.n {{ $claim->txtBankAccountHolder }}</p>
          </div>
        </div>
      </div>

      <!-- Itemized Table of Voucher Transactions -->
      <div class="py-6 border-b border-slate-200">
        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-3">
          Rincian Transaksi Voucher Karyawan yang Ditagihkan:
        </h2>

        <table class="w-full text-left text-xs">
          <thead>
            <tr class="border-b border-slate-300 text-slate-500 uppercase tracking-wider text-[10px]">
              <th class="py-2">No</th>
              <th class="py-2">No Invoice Transaksi</th>
              <th class="py-2">Waktu</th>
              <th class="py-2">Nama Karyawan (NIK)</th>
              <th class="py-2 text-right">Nominal Voucher</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @foreach($claim->details as $index => $item)
              @php $sale = $item->sale; @endphp
              <tr>
                <td class="py-2.5 text-slate-400">{{ $index + 1 }}</td>
                <td class="py-2.5 font-mono font-semibold text-slate-800">{{ $sale->txtInvoiceNumber }}</td>
                <td class="py-2.5 text-slate-600">{{ $sale->dtmInserted->format('d/m/Y H:i') }}</td>
                <td class="py-2.5 text-slate-800">
                  <span class="font-semibold">{{ $sale->employee?->txtEmployeeName ?? 'Karyawan KMI' }}</span>
                  <span class="text-slate-400 font-mono">({{ $sale->employee?->txtEmployeeNik ?? '-' }})</span>
                </td>
                <td class="py-2.5 text-right font-bold text-slate-900">
                  Rp {{ number_format($item->floatVoucherAmount, 0, ',', '.') }}
                </td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr class="border-t-2 border-slate-300 font-bold text-slate-900">
              <td colspan="4" class="py-3 text-right text-xs">TOTAL PENGGANTIAN DANA (REIMBURSEMENT):</td>
              <td class="py-3 text-right text-base font-extrabold text-emerald-800">
                Rp {{ number_format($claim->floatTotalClaimAmount, 0, ',', '.') }}
              </td>
            </tr>
          </tfoot>
        </table>
      </div>

      <!-- Terbilang & Catatan -->
      <div class="py-4 text-xs text-slate-600 bg-emerald-50/50 p-4 rounded-xl border border-emerald-100 mt-4">
        <p class="font-bold text-emerald-950">Catatan Panitia Festival:</p>
        <p class="mt-0.5">Dana akan ditransfer oleh bagian keuangan PT Kalbe Morinaga Indonesia ke rekening mitra UMKM yang tertera paling lambat 1-2 hari kerja setelah verifikasi invoice ini.</p>
      </div>

      <!-- Signature Approval Box -->
      <div class="grid grid-cols-3 gap-6 pt-12 text-center text-xs">
        <div>
          <p class="text-slate-500 mb-16">Diajukan oleh (Mitra UMKM),</p>
          <div class="border-b border-slate-400 w-40 mx-auto"></div>
          <p class="font-bold text-slate-900 mt-1">{{ $claim->merchant->txtOwnerName }}</p>
          <p class="text-[11px] text-slate-500">{{ $claim->merchant->txtMerchantName }}</p>
        </div>

        <div>
          <p class="text-slate-500 mb-16">Diverifikasi Panitia KMI,</p>
          <div class="border-b border-slate-400 w-40 mx-auto"></div>
          <p class="font-bold text-slate-900 mt-1">Panitia Hoya-Hoya Festival</p>
          <p class="text-[11px] text-slate-500">PT Kalbe Morinaga Indonesia</p>
        </div>

        <div>
          <p class="text-slate-500 mb-16">Disetujui Bagian Keuangan,</p>
          <div class="border-b border-slate-400 w-40 mx-auto"></div>
          <p class="font-bold text-slate-900 mt-1">Finance & Accounting</p>
          <p class="text-[11px] text-slate-500">PT Kalbe Morinaga Indonesia</p>
        </div>
      </div>

    </div>
  </div>

</body>
</html>

