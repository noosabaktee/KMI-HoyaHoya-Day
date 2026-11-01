@extends('layouts.app')

@section('header_title', 'Dashboard Perputaran Ekonomi - Hoya-Hoya Day Festival')

@section('content')
<div class="space-y-6">

  <!-- Welcome Banner -->
  <div class="relative overflow-hidden bg-gradient-to-r from-kmi-700 via-kmi-600 to-emerald-800 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-kmi-600/20">
    <div class="relative z-10 max-w-3xl">
      <div class="inline-flex items-center space-x-2 px-3 py-1 bg-white/15 backdrop-blur-md rounded-full text-xs font-semibold mb-3">
        <i class='bx bx-calendar-event text-festive-500'></i>
        <span>Hoya-Hoya Day Festival 2026 &bull; PT Kalbe Morinaga Indonesia</span>
      </div>
      <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Monitoring Perputaran Ekonomi & Voucher Digital</h1>
      <p class="text-emerald-100 text-sm mt-2 leading-relaxed">
        Sistem pemantauan real-time transaksi digital voucher karyawan KMI dan belanja tunai guna mendukung pemberdayaan UMKM lokal.
      </p>
    </div>
    <div class="absolute -right-10 -bottom-10 opacity-15 text-white pointer-events-none">
      <i class='bx bxs-store text-[220px]'></i>
    </div>
  </div>

  <!-- Key Metrics 4-Grid -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
    <!-- Total Economic Turnover -->
    <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex flex-col justify-between">
      <div class="flex items-center justify-between">
        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Perputaran Ekonomi</span>
        <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-kmi-600 flex items-center justify-center text-xl">
          <i class='bx bx-line-chart'></i>
        </div>
      </div>
      <div class="mt-4">
        <div class="text-2xl font-extrabold text-slate-900">Rp {{ number_format($totalEconomicTurnover, 0, ',', '.') }}</div>
        <p class="text-xs text-slate-500 mt-1">Total transaksi: <span class="font-bold text-slate-700">{{ $totalTransactions }}</span> transaksi</p>
      </div>
    </div>

    <!-- Voucher Used -->
    <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex flex-col justify-between">
      <div class="flex items-center justify-between">
        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Voucher Terpakai</span>
        <div class="w-10 h-10 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center text-xl">
          <i class='bx bx-barcode-reader'></i>
        </div>
      </div>
      <div class="mt-4">
        <div class="text-2xl font-extrabold text-sky-900">Rp {{ number_format($totalVoucherUsed, 0, ',', '.') }}</div>
        <p class="text-xs text-slate-500 mt-1">Alokasi Awal: <span class="font-bold text-slate-700">Rp {{ number_format($totalDistributedAmount, 0, ',', '.') }}</span></p>
      </div>
    </div>

    <!-- Cash Spent -->
    <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex flex-col justify-between">
      <div class="flex items-center justify-between">
        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Belanja Tunai (Cash)</span>
        <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
          <i class='bx bx-money'></i>
        </div>
      </div>
      <div class="mt-4">
        <div class="text-2xl font-extrabold text-amber-900">Rp {{ number_format($totalCash, 0, ',', '.') }}</div>
        <p class="text-xs text-slate-500 mt-1">Uang tunai tambahan & umum</p>
      </div>
    </div>

    <!-- Active Employees & Vouchers -->
    <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm flex flex-col justify-between">
      <div class="flex items-center justify-between">
        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Status Voucher Karyawan</span>
        <div class="w-10 h-10 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl">
          <i class='bx bx-user-check'></i>
        </div>
      </div>
      <div class="mt-4">
        <div class="text-2xl font-extrabold text-purple-900">{{ $voucherActiveCount }} <span class="text-xs font-normal text-slate-500">/ {{ $totalVouchers }} Aktif</span></div>
        <div class="flex items-center space-x-2 text-[11px] text-slate-500 mt-1">
          <span class="text-amber-600 font-semibold">{{ $voucherSentCount }} Terkirim</span> &bull;
          <span class="text-emerald-600 font-semibold">{{ $voucherActiveCount }} Aktif</span> &bull;
          <span class="text-slate-400">{{ $voucherFinishedCount }} Habis</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Charts Row -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Merchant Performance Bar Chart -->
    <div class="lg:col-span-2 bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
      <div class="flex items-center justify-between mb-4">
        <div>
          <h2 class="text-base font-bold text-slate-900">Perputaran Ekonomi per Booth UMKM</h2>
          <p class="text-xs text-slate-500">Perbandingan transaksi voucher dan tunai masing-masing stand</p>
        </div>
        <a href="{{ route('admin.merchants.index') }}" class="text-xs font-bold text-kmi-600 hover:text-kmi-700">Lihat Semua &rarr;</a>
      </div>
      <div class="h-72">
        <canvas id="merchantChart"></canvas>
      </div>
    </div>

    <!-- Payment Mix Donut Chart -->
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col justify-between">
      <div>
        <h2 class="text-base font-bold text-slate-900">Metode Pembayaran</h2>
        <p class="text-xs text-slate-500 mb-4">Komposisi Voucher vs Uang Tunai</p>
        <div class="h-56 relative flex items-center justify-center">
          <canvas id="paymentMixChart"></canvas>
        </div>
      </div>
      <div class="pt-4 border-t border-slate-100 space-y-2 text-xs">
        <div class="flex justify-between items-center">
          <span class="flex items-center space-x-2 text-slate-600">
            <span class="w-3 h-3 rounded-full bg-sky-500 inline-block"></span>
            <span>Voucher Karyawan:</span>
          </span>
          <span class="font-bold text-slate-900">Rp {{ number_format($totalVoucherUsed, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between items-center">
          <span class="flex items-center space-x-2 text-slate-600">
            <span class="w-3 h-3 rounded-full bg-amber-500 inline-block"></span>
            <span>Uang Tunai (Cash):</span>
          </span>
          <span class="font-bold text-slate-900">Rp {{ number_format($totalCash, 0, ',', '.') }}</span>
        </div>
      </div>
    </div>
  </div>

  <!-- UMKM Stand Leaderboard & Recent Transactions -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Merchant Stand Leaderboard -->
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-bold text-slate-900">Peringkat Booth UMKM</h2>
        <span class="text-xs px-2.5 py-1 bg-kmi-50 text-kmi-700 rounded-full font-semibold">{{ $totalMerchants }} Booth</span>
      </div>
      <div class="space-y-3">
        @forelse($merchantPerformances as $index => $m)
          <div class="p-3 rounded-2xl border border-slate-100 bg-slate-50/50 hover:bg-slate-50 transition-colors flex items-center justify-between">
            <div class="flex items-center space-x-3 min-w-0">
              <div class="w-8 h-8 rounded-xl flex items-center justify-center font-extrabold text-xs {{ $index === 0 ? 'bg-amber-400 text-amber-950' : ($index === 1 ? 'bg-slate-200 text-slate-700' : 'bg-slate-100 text-slate-600') }}">
                #{{ $index + 1 }}
              </div>
              <div class="min-w-0">
                <p class="text-xs font-bold text-slate-900 truncate">{{ $m->txtMerchantName }}</p>
                <p class="text-[11px] text-slate-500">{{ $m->txtBoothNumber }} &bull; {{ $m->sales_count }} trx</p>
              </div>
            </div>
            <div class="text-right">
              <span class="text-xs font-extrabold text-emerald-800">Rp {{ number_format($m->total_revenue ?? 0, 0, ',', '.') }}</span>
            </div>
          </div>
        @empty
          <p class="text-xs text-slate-400 text-center py-6">Belum ada data penjualan UMKM.</p>
        @endforelse
      </div>
    </div>

    <!-- Recent Transactions Table -->
    <div class="lg:col-span-2 bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
      <div class="flex items-center justify-between mb-4">
        <div>
          <h2 class="text-base font-bold text-slate-900">Transaksi Terkini</h2>
          <p class="text-xs text-slate-500">10 transaksi festival terbaru yang berhasil tercatat</p>
        </div>
        <a href="{{ route('admin.reports.index') }}" class="text-xs font-bold text-kmi-600 hover:text-kmi-700">Lihat Semua Laporan &rarr;</a>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead>
            <tr class="text-slate-400 border-b border-slate-100 uppercase tracking-wider text-[10px]">
              <th class="pb-3">Invoice</th>
              <th class="pb-3">Booth UMKM</th>
              <th class="pb-3">Pelanggan</th>
              <th class="pb-3">Metode</th>
              <th class="pb-3 text-right">Total</th>
              <th class="pb-3 text-right">Waktu</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50">
            @forelse($recentTransactions as $trx)
              <tr class="hover:bg-slate-50/70">
                <td class="py-3 font-semibold text-slate-800">{{ $trx->txtInvoiceNumber }}</td>
                <td class="py-3 text-slate-600">{{ $trx->merchant?->txtMerchantName ?? '-' }}</td>
                <td class="py-3">
                  <span class="font-medium text-slate-800">{{ $trx->employee?->txtEmployeeName ?? 'Umum/Tunai' }}</span>
                </td>
                <td class="py-3">
                  @if($trx->txtPaymentType === 'VOUCHER')
                    <span class="px-2 py-0.5 bg-sky-50 text-sky-700 rounded-md font-semibold text-[10px]">Voucher</span>
                  @elseif($trx->txtPaymentType === 'SPLIT')
                    <span class="px-2 py-0.5 bg-purple-50 text-purple-700 rounded-md font-semibold text-[10px]">Voucher + Cash</span>
                  @else
                    <span class="px-2 py-0.5 bg-amber-50 text-amber-700 rounded-md font-semibold text-[10px]">Tunai</span>
                  @endif
                </td>
                <td class="py-3 text-right font-bold text-slate-900">Rp {{ number_format($trx->floatTotalAmount, 0, ',', '.') }}</td>
                <td class="py-3 text-right text-slate-400">{{ $trx->dtmInserted->format('H:i') }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="py-6 text-center text-slate-400">Belum ada transaksi berlangsung.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
  // Merchant Performance Chart
  const merchantNames = @json($merchantPerformances->pluck('txtMerchantName'));
  const merchantVouchers = @json($merchantPerformances->pluck('total_voucher'));
  const merchantCashes = @json($merchantPerformances->pluck('total_cash'));

  const ctxMerchant = document.getElementById('merchantChart')?.getContext('2d');
  if (ctxMerchant) {
    new Chart(ctxMerchant, {
      type: 'bar',
      data: {
        labels: merchantNames,
        datasets: [
          {
            label: 'Voucher Karyawan',
            data: merchantVouchers,
            backgroundColor: '#0284c7',
            borderRadius: 8,
          },
          {
            label: 'Tunai (Cash)',
            data: merchantCashes,
            backgroundColor: '#f59e0b',
            borderRadius: 8,
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: { stacked: true, grid: { display: false } },
          y: { stacked: true, beginAtZero: true, grid: { color: '#f1f5f9' } }
        },
        plugins: {
          legend: { position: 'top', align: 'end' }
        }
      }
    });
  }

  // Payment Mix Donut Chart
  const ctxPayment = document.getElementById('paymentMixChart')?.getContext('2d');
  if (ctxPayment) {
    new Chart(ctxPayment, {
      type: 'doughnut',
      data: {
        labels: ['Voucher Karyawan', 'Tunai (Cash)'],
        datasets: [{
          data: [{{ $totalVoucherUsed }}, {{ $totalCash }}],
          backgroundColor: ['#0284c7', '#f59e0b'],
          borderWidth: 0,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '72%',
        plugins: {
          legend: { display: false }
        }
      }
    });
  }
</script>
@endpush

