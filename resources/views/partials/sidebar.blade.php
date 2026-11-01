<aside id="sidebar" class="w-64 bg-white border-r border-slate-200 flex flex-col justify-between hidden md:flex transition-all duration-300 z-30">
  <div>
    <!-- Logo & Festival Header -->
    <div class="p-5 border-b border-slate-100 flex items-center space-x-3 bg-gradient-to-r from-kmi-50 to-white">
      <div class="w-10 h-10 rounded-xl bg-kmi-600 flex items-center justify-center text-white shadow-md shadow-kmi-600/30 font-extrabold text-lg tracking-wider">
        KMI
      </div>
      <div>
        <h1 class="text-sm font-bold text-slate-900 leading-tight">Hoya-Hoya Day</h1>
        <p class="text-[11px] font-medium text-kmi-700">Kalbe Morinaga Indonesia</p>
      </div>
    </div>

    <!-- Navigation Links -->
    <nav class="p-4 space-y-1.5">
      @if(auth()->user()->isAdmin())
        <div class="px-3 pt-2 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-400">Panel Panitia</div>

        <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-kmi-600 text-white shadow-sm shadow-kmi-600/30' : 'text-slate-600 hover:bg-slate-100' }}">
          <i class='bx bxs-dashboard text-xl'></i>
          <span>Perputaran Ekonomi</span>
        </a>

        <a href="{{ route('admin.vouchers.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-colors {{ request()->routeIs('admin.vouchers.*') ? 'bg-kmi-600 text-white shadow-sm shadow-kmi-600/30' : 'text-slate-600 hover:bg-slate-100' }}">
          <i class='bx bx-barcode-reader text-xl'></i>
          <span>Voucher & Barcode</span>
        </a>

        <a href="{{ route('admin.merchants.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-colors {{ request()->routeIs('admin.merchants.*') ? 'bg-kmi-600 text-white shadow-sm shadow-kmi-600/30' : 'text-slate-600 hover:bg-slate-100' }}">
          <i class='bx bx-store-alt text-xl'></i>
          <span>Mitra UMKM Binaan</span>
        </a>

        <a href="{{ route('admin.claims.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-colors {{ request()->routeIs('admin.claims.*') ? 'bg-kmi-600 text-white shadow-sm shadow-kmi-600/30' : 'text-slate-600 hover:bg-slate-100' }}">
          <i class='bx bx-receipt text-xl'></i>
          <span>Klaim Reimbursement</span>
        </a>

        <a href="{{ route('admin.reports.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-colors {{ request()->routeIs('admin.reports.*') ? 'bg-kmi-600 text-white shadow-sm shadow-kmi-600/30' : 'text-slate-600 hover:bg-slate-100' }}">
          <i class='bx bx-line-chart text-xl'></i>
          <span>Laporan Festival</span>
        </a>

        <div class="px-3 pt-4 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-400">Pusat Informasi</div>

        <a href="{{ route('admin.docs.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-colors {{ request()->routeIs('admin.docs.*') ? 'bg-festive-500 text-white shadow-sm shadow-festive-500/30' : 'text-slate-700 bg-amber-50 hover:bg-amber-100 border border-amber-200' }}">
          <i class='bx bx-book-open text-xl text-festive-600 {{ request()->routeIs('admin.docs.*') ? '!text-white' : '' }}'></i>
          <span class="font-semibold">Dokumentasi Sistem</span>
        </a>

      @else
        <div class="px-3 pt-2 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-400">Booth: {{ auth()->user()->merchant->txtBoothNumber ?? 'UMKM' }}</div>

        <a href="{{ route('umkm.pos') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-colors {{ request()->routeIs('umkm.pos') ? 'bg-kmi-600 text-white shadow-sm shadow-kmi-600/30' : 'text-slate-600 hover:bg-slate-100' }}">
          <i class='bx bx-scan text-xl'></i>
          <span>Kasir POS & Scan</span>
        </a>

        <a href="{{ route('umkm.products.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-colors {{ request()->routeIs('umkm.products.*') ? 'bg-kmi-600 text-white shadow-sm shadow-kmi-600/30' : 'text-slate-600 hover:bg-slate-100' }}">
          <i class='bx bx-package text-xl'></i>
          <span>Produk & Stok</span>
        </a>

        <a href="{{ route('umkm.sales.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-colors {{ request()->routeIs('umkm.sales.*') ? 'bg-kmi-600 text-white shadow-sm shadow-kmi-600/30' : 'text-slate-600 hover:bg-slate-100' }}">
          <i class='bx bx-history text-xl'></i>
          <span>Riwayat Penjualan</span>
        </a>

        <a href="{{ route('umkm.claims.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-colors {{ request()->routeIs('umkm.claims.*') ? 'bg-kmi-600 text-white shadow-sm shadow-kmi-600/30' : 'text-slate-600 hover:bg-slate-100' }}">
          <i class='bx bx-wallet text-xl'></i>
          <span>Klaim Saldo Voucher</span>
        </a>
      @endif
    </nav>
  </div>

  <!-- Bottom Profile & Logout -->
  <div class="p-4 border-t border-slate-100 bg-slate-50/50">
    <div class="flex items-center justify-between">
      <div class="flex items-center space-x-3 min-w-0">
        <div class="w-9 h-9 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-sm">
          {{ strtoupper(substr(auth()->user()->txtFullName ?? 'U', 0, 1)) }}
        </div>
        <div class="min-w-0">
          <p class="text-xs font-bold text-slate-800 truncate">{{ auth()->user()->txtFullName }}</p>
          <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold {{ auth()->user()->isAdmin() ? 'bg-kmi-100 text-kmi-800' : 'bg-morinaga-50 text-morinaga-700 border border-morinaga-500/20' }}">
            {{ auth()->user()->isAdmin() ? 'Panitia KMI' : (auth()->user()->merchant->txtMerchantName ?? 'Mitra UMKM') }}
          </span>
        </div>
      </div>

      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" title="Keluar Sistem" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
          <i class='bx bx-log-out text-xl'></i>
        </button>
      </form>
    </div>
  </div>
</aside>
