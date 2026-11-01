<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Hoya-Hoya Day Festival | PT Kalbe Morinaga Indonesia</title>
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
<body class="bg-gradient-to-br from-slate-50 via-emerald-50/40 to-slate-100 min-h-screen flex items-center justify-center p-4">

  <div class="max-w-md w-full">
    <!-- Header Card -->
    <div class="text-center mb-8">
      <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-kmi-600 text-white font-extrabold text-2xl shadow-xl shadow-kmi-600/30 mb-4">
        KMI
      </div>
      <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Hoya-Hoya Day Festival</h1>
      <p class="text-sm font-semibold text-kmi-700 mt-1">PT Kalbe Morinaga Indonesia</p>
      <p class="text-xs text-slate-500 mt-1">Sistem POS Digital & Voucher Karyawan Pemberdayaan UMKM Lokal</p>
    </div>

    <!-- Login Form Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-200/60 border border-slate-100">
      @if ($errors->any())
        <div class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
          <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      @if (session('success'))
        <div class="mb-5 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs">
          {{ session('success') }}
        </div>
      @endif

      <form action="{{ route('login.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
          <label for="username" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Pengguna / Username</label>
          <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
              <i class='bx bx-user text-lg'></i>
            </span>
            <input type="text" name="username" id="username" value="{{ old('username', 'admin') }}" required
              class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-kmi-600 focus:bg-white transition-colors"
              placeholder="Masukkan username Anda">
          </div>
        </div>

        <div>
          <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kata Sandi</label>
          <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
              <i class='bx bx-lock-alt text-lg'></i>
            </span>
            <input type="password" name="password" id="password" value="password" required
              class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-kmi-600 focus:bg-white transition-colors"
              placeholder="••••••••">
          </div>
        </div>

        <div class="flex items-center justify-between text-xs text-slate-500 pt-1">
          <label class="flex items-center space-x-2 cursor-pointer">
            <input type="checkbox" name="remember" class="rounded text-kmi-600 focus:ring-kmi-500">
            <span>Ingat saya</span>
          </label>
          <span class="text-kmi-600 font-medium">Festival 2026</span>
        </div>

        <button type="submit"
          class="w-full py-3.5 px-4 bg-kmi-600 hover:bg-kmi-700 text-white font-bold rounded-xl shadow-lg shadow-kmi-600/30 transition-all text-sm flex items-center justify-center space-x-2">
          <span>Masuk ke Sistem</span>
          <i class='bx bx-right-arrow-alt text-lg'></i>
        </button>
      </form>

      <!-- Quick Demo Access Section -->
      <div class="mt-8 pt-6 border-t border-slate-100">
        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider text-center mb-3">Akun Demo Cepat (Klik untuk Mengisi)</p>
        <div class="grid grid-cols-1 gap-2">
          <button type="button" onclick="fillLogin('admin', 'password')"
            class="p-2.5 rounded-xl border border-emerald-200 bg-emerald-50/60 hover:bg-emerald-100/70 text-left transition-colors flex items-center justify-between">
            <div>
              <p class="text-xs font-bold text-emerald-900">👑 Admin / Panitia Festival KMI</p>
              <p class="text-[11px] text-emerald-700">User: <code class="font-mono">admin</code> | Pass: <code class="font-mono">password</code></p>
            </div>
            <i class='bx bx-log-in text-emerald-600 text-lg'></i>
          </button>

          <button type="button" onclick="fillLogin('umkm_siti', 'password')"
            class="p-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-left transition-colors flex items-center justify-between">
            <div>
              <p class="text-xs font-bold text-slate-900">🍲 Booth A-01: Dapur Nusantara Bu Siti</p>
              <p class="text-[11px] text-slate-600">User: <code class="font-mono">umkm_siti</code> | Pass: <code class="font-mono">password</code></p>
            </div>
            <i class='bx bx-log-in text-slate-400 text-lg'></i>
          </button>

          <button type="button" onclick="fillLogin('umkm_kopi', 'password')"
            class="p-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-left transition-colors flex items-center justify-between">
            <div>
              <p class="text-xs font-bold text-slate-900">☕ Booth B-03: Kopi Klangenan Morinaga</p>
              <p class="text-[11px] text-slate-600">User: <code class="font-mono">umkm_kopi</code> | Pass: <code class="font-mono">password</code></p>
            </div>
            <i class='bx bx-log-in text-slate-400 text-lg'></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Footer Copyright -->
    <p class="text-center text-xs text-slate-400 mt-6">
      &copy; 2026 PT Kalbe Morinaga Indonesia. All rights reserved.
    </p>
  </div>

  <script>
    function fillLogin(user, pass) {
      document.getElementById('username').value = user;
      document.getElementById('password').value = pass;
    }
  </script>
</body>
</html>
