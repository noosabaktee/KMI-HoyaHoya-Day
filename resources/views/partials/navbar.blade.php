<header class="h-14 sm:h-16 bg-white border-b border-slate-200 flex items-center justify-between px-3 sm:px-6 z-20 shrink-0">
  <div class="flex items-center space-x-2 sm:space-x-3 min-w-0 flex-1 mr-2">
    <!-- Mobile Hamburger Toggle -->
    <button id="mobileMenuBtn" class="md:hidden p-1.5 -ml-1 rounded-lg text-slate-600 hover:bg-slate-100 focus:outline-none shrink-0" aria-label="Buka Menu">
      <i class='bx bx-menu text-2xl'></i>
    </button>

    <div class="flex items-center space-x-2 min-w-0 flex-1">
      <span class="inline-block w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-full bg-emerald-500 animate-pulse shrink-0"></span>
      <h2 class="text-xs sm:text-sm md:text-base font-bold text-slate-900 tracking-tight truncate" title="@yield('header_title', 'Hoya-Hoya Day Festival 2026')">
        @yield('header_title', 'Hoya-Hoya Day Festival 2026')
      </h2>
    </div>
  </div>

  <div class="flex items-center space-x-2 sm:space-x-3 shrink-0">
    <!-- Event Badge (visible on tablet and desktop) -->
    <div class="hidden lg:flex items-center space-x-1.5 px-3 py-1 bg-kmi-50 border border-kmi-200/60 rounded-full text-xs font-semibold text-kmi-700">
      <i class='bx bxs-badge-check text-kmi-600 text-sm'></i>
      <span>PT Kalbe Morinaga Indonesia</span>
    </div>

    <!-- User Profile Badge -->
    <div class="flex items-center space-x-1.5 bg-slate-50 border border-slate-200/80 py-1 px-2.5 rounded-xl">
      <div class="w-6 h-6 rounded-lg bg-kmi-600 text-white flex items-center justify-center font-bold text-[10px] shrink-0">
        {{ strtoupper(substr(auth()->user()->txtUsername, 0, 1)) }}
      </div>
      <span class="text-xs font-bold text-slate-700 max-w-[85px] sm:max-w-none truncate">{{ auth()->user()->txtUsername }}</span>
    </div>
  </div>
</header>

<!-- Mobile Navigation Drawer -->
<div id="mobileDrawer" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden transition-opacity">
  <div class="w-72 bg-white h-full shadow-2xl flex flex-col justify-between transform transition-transform">
    <div>
      <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-kmi-50">
        <div class="flex items-center space-x-2">
          <div class="w-8 h-8 rounded-lg bg-kmi-600 text-white font-bold flex items-center justify-center text-sm">KMI</div>
          <span class="font-bold text-sm text-slate-900">Hoya-Hoya Festival</span>
        </div>
        <button id="closeDrawerBtn" class="p-2 text-slate-400 hover:text-slate-700">
          <i class='bx bx-x text-2xl'></i>
        </button>
      </div>

      <nav class="p-4 space-y-1">
        @if(auth()->user()->isAdmin())
          <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm text-slate-700 hover:bg-slate-100">
            <i class='bx bxs-dashboard text-lg'></i>
            <span>Dashboard Perputaran Ekonomi</span>
          </a>
          <a href="{{ route('admin.vouchers.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm text-slate-700 hover:bg-slate-100">
            <i class='bx bx-barcode-reader text-lg'></i>
            <span>Voucher & Barcode</span>
          </a>
          <a href="{{ route('admin.merchants.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm text-slate-700 hover:bg-slate-100">
            <i class='bx bx-store-alt text-lg'></i>
            <span>Mitra UMKM Binaan</span>
          </a>
          <a href="{{ route('admin.claims.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm text-slate-700 hover:bg-slate-100">
            <i class='bx bx-receipt text-lg'></i>
            <span>Klaim Reimbursement</span>
          </a>
          <a href="{{ route('admin.reports.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm text-slate-700 hover:bg-slate-100">
            <i class='bx bx-line-chart text-lg'></i>
            <span>Laporan Festival</span>
          </a>
          <a href="{{ route('admin.master.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm text-slate-700 hover:bg-slate-100">
            <i class='bx bx-data text-lg'></i>
            <span>Master Data</span>
          </a>
          <a href="{{ route('admin.docs.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-semibold text-sm bg-amber-50 text-amber-900 border border-amber-200">
            <i class='bx bx-book-open text-lg text-amber-600'></i>
            <span>Dokumentasi Sistem</span>
          </a>
        @else
          <a href="{{ route('umkm.pos') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm text-slate-700 hover:bg-slate-100">
            <i class='bx bx-scan text-lg'></i>
            <span>Kasir POS & Scan</span>
          </a>
          <a href="{{ route('umkm.products.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm text-slate-700 hover:bg-slate-100">
            <i class='bx bx-package text-lg'></i>
            <span>Produk & Stok</span>
          </a>
          <a href="{{ route('umkm.sales.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm text-slate-700 hover:bg-slate-100">
            <i class='bx bx-history text-lg'></i>
            <span>Riwayat Penjualan</span>
          </a>
          <a href="{{ route('umkm.claims.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm text-slate-700 hover:bg-slate-100">
            <i class='bx bx-wallet text-lg'></i>
            <span>Klaim Saldo Voucher</span>
          </a>
        @endif
      </nav>
    </div>

    <div class="p-4 border-t border-slate-100">
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="w-full flex items-center justify-center space-x-2 px-4 py-2.5 rounded-xl bg-rose-50 text-rose-700 font-semibold text-sm hover:bg-rose-100">
          <i class='bx bx-log-out text-lg'></i>
          <span>Keluar Sistem</span>
        </button>
      </form>
    </div>
  </div>
</div>

<script>
  const mobileMenuBtn = document.getElementById('mobileMenuBtn');
  const closeDrawerBtn = document.getElementById('closeDrawerBtn');
  const mobileDrawer = document.getElementById('mobileDrawer');

  mobileMenuBtn?.addEventListener('click', () => mobileDrawer?.classList.remove('hidden'));
  closeDrawerBtn?.addEventListener('click', () => mobileDrawer?.classList.add('hidden'));
  mobileDrawer?.addEventListener('click', (e) => {
    if (e.target === mobileDrawer) mobileDrawer.classList.add('hidden');
  });
</script>

