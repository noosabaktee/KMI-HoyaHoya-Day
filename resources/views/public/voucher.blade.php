<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Voucher Digital {{ $voucher->employee->txtEmployeeName ?? 'Karyawan' }} - PT Kalbe Morinaga Indonesia</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            kmi: {
              50: '#f0fdf4',
              100: '#dcfce7',
              600: '#008542',
              700: '#006d36',
              800: '#065f46',
            },
            morinaga: {
              600: '#0891b2',
            },
            festive: {
              500: '#f59e0b',
              600: '#d97706',
            }
          },
          fontFamily: {
            sans: ['"Plus Jakarta Sans"', 'sans-serif'],
          }
        }
      }
    }
  </script>
</head>
<body class="bg-gradient-to-br from-slate-100 via-emerald-50/50 to-slate-200 min-h-screen p-4 flex flex-col items-center justify-center font-sans">

  <div class="max-w-md w-full space-y-4">

    <!-- Top Branding -->
    <div class="text-center">
      <div class="inline-flex items-center space-x-2 px-3 py-1 bg-white/80 backdrop-blur-md rounded-full text-xs font-semibold text-kmi-700 shadow-sm border border-emerald-100 mb-2">
        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
        <span>Hoya-Hoya Day Festival 2026</span>
      </div>
      <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">PT Kalbe Morinaga Indonesia</h1>
      <p class="text-xs text-slate-500">Voucher Belanja Digital Karyawan</p>
    </div>

    <!-- Official Card Container -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-100 relative overflow-hidden">
      <!-- Decorative background orb -->
      <div class="absolute -right-16 -top-16 w-36 h-36 bg-emerald-100 rounded-full blur-2xl opacity-50 pointer-events-none"></div>

      <!-- Card Top Info -->
      <div class="flex items-start justify-between pb-4 border-b border-slate-100">
        <div>
          <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Pemegang Voucher:</span>
          <h2 class="text-lg font-extrabold text-slate-900 leading-tight">{{ $voucher->employee->txtEmployeeName }}</h2>
          <p class="text-xs font-mono font-bold text-morinaga-600 mt-0.5">NIK: {{ $voucher->employee->txtEmployeeNik }}</p>
          <p class="text-[11px] text-slate-400">{{ $voucher->employee->txtDepartment ?? 'Karyawan KMI' }}</p>
        </div>

        <div class="text-right">
          @if($voucher->txtStatus === 'Aktif')
            <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-kmi-800 flex items-center space-x-1 shadow-sm">
              <i class='bx bx-check-circle'></i>
              <span>AKTIF</span>
            </span>
          @elseif($voucher->txtStatus === 'Terkirim')
            <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800 flex items-center space-x-1 shadow-sm">
              <i class='bx bx-time'></i>
              <span>TERKIRIM</span>
            </span>
          @elseif($voucher->txtStatus === 'Habis')
            <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-slate-100 text-slate-600">
              HABIS
            </span>
          @else
            <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-sky-100 text-sky-800">
              {{ $voucher->txtStatus }}
            </span>
          @endif
        </div>
      </div>

      <!-- Balance Box -->
      <div class="my-5 p-4 rounded-2xl bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-100 flex items-center justify-between">
        <div>
          <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-800">Sisa Saldo Voucher:</span>
          <div class="text-2xl sm:text-3xl font-extrabold text-kmi-800 mt-0.5">
            Rp {{ number_format($voucher->floatCurrentBalance, 0, ',', '.') }}
          </div>
          <span class="text-[10px] text-slate-400">Alokasi Awal: Rp {{ number_format($voucher->floatInitialBalance, 0, ',', '.') }}</span>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-kmi-600 text-white flex items-center justify-center text-2xl shadow-md shadow-kmi-600/30">
          <i class='bx bx-wallet'></i>
        </div>
      </div>

      <!-- Scannable Barcode & QR Code Section -->
      <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 text-center space-y-2">
        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tunjukkan Kartu QR Ini ke Kasir UMKM:</span>

        @if($voucher->txtBarcodeCardPath && file_exists(storage_path('app/public/' . $voucher->txtBarcodeCardPath)))
          <div class="flex justify-center p-2 bg-white rounded-xl border border-slate-200">
            <img src="{{ asset('storage/' . $voucher->txtBarcodeCardPath) }}" alt="QRIS Card" class="w-full rounded-lg shadow-sm">
          </div>
        @else
          <div class="p-4 bg-white rounded-xl border border-slate-200 text-slate-600 text-xs font-semibold">
            Kartu QR sedang diproses.
          </div>
        @endif
      </div>

      <!-- Instructions & Terms -->
      <div class="mt-5 space-y-2 text-xs text-slate-500 border-t border-slate-100 pt-4">
        @if($voucher->txtStatus === 'Terkirim')
          <div class="p-3 bg-amber-50 border border-amber-200 text-amber-900 rounded-xl text-xs space-y-1">
            <p class="font-bold">⚠️ Menunggu Konfirmasi Balasan WhatsApp:</p>
            <p>Silakan balas pesan WhatsApp panitia yang Anda terima (misal ketik: <b>"Diterima"</b> atau <b>"OK"</b>) untuk mengaktifkan voucher Anda sebelum berbelanja.</p>
          </div>
        @endif

        <div class="flex items-start space-x-2">
          <i class='bx bx-check text-kmi-600 text-base shrink-0 mt-0.5'></i>
          <span>Tunjukkan barcode kartu ini ke kasir stand/booth UMKM festival saat memesan.</span>
        </div>
        <div class="flex items-start space-x-2">
          <i class='bx bx-check text-kmi-600 text-base shrink-0 mt-0.5'></i>
          <span>Dapat digunakan bertahap hingga saldo Rp 0.</span>
        </div>
        <div class="flex items-start space-x-2">
          <i class='bx bx-check text-kmi-600 text-base shrink-0 mt-0.5'></i>
          <span>Jika belanja melebihi sisa saldo, Anda cukup membayar kekurangannya secara tunai ke UMKM.</span>
        </div>
      </div>

      <!-- Action Button: Download Card Image -->
      @if($voucher->txtBarcodeCardPath)
        <div class="mt-6">
          <a href="{{ asset('storage/' . $voucher->txtBarcodeCardPath) }}" download="Voucher_KMI_{{ $voucher->txtVoucherCode }}.png"
            class="w-full py-3 bg-kmi-600 hover:bg-kmi-700 text-white font-bold rounded-2xl text-xs flex items-center justify-center space-x-2 shadow-lg shadow-kmi-600/30 transition-all">
            <i class='bx bx-download text-lg'></i>
            <span>Simpan Kartu ke Galeri HP</span>
          </a>
        </div>
      @endif

    </div>

    <!-- Footer Copyright -->
    <p class="text-center text-[11px] text-slate-400">
      &copy; 2026 PT Kalbe Morinaga Indonesia &bull; Hoya-Hoya Day Festival
    </p>

  </div>

</body>
</html>

