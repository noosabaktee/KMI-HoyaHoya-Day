@extends('layouts.app')

@section('header_title', 'Kasir POS - ' . ($merchant->txtMerchantName ?? 'Booth UMKM'))

@section('content')
<div class="h-[calc(100vh-6.5rem)] flex flex-col lg:flex-row gap-6 -m-2 sm:-m-4">

  <!-- Left: Product Catalog Section -->
  <div class="flex-1 flex flex-col bg-white rounded-3xl p-5 border border-slate-100 shadow-sm overflow-hidden">
    
    <!-- Top Filter Bar: Search & Categories -->
    <div class="space-y-3 pb-4 border-b border-slate-100">
      <div class="flex items-center justify-between gap-3">
        <div class="relative flex-1">
          <i class='bx bx-search absolute left-3.5 top-3 text-slate-400 text-lg'></i>
          <input type="text" id="productSearchInput" onkeyup="filterProducts()" placeholder="Cari nama menu / produk..."
            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs focus:outline-none focus:border-kmi-600 focus:bg-white">
        </div>
        <div class="px-3 py-2 bg-emerald-50 text-kmi-800 rounded-2xl text-xs font-bold border border-emerald-100 hidden sm:flex items-center space-x-1">
          <i class='bx bx-store-alt text-base'></i>
          <span>{{ $merchant->txtBoothNumber }}</span>
        </div>
      </div>

      <!-- Category Filter Pills -->
      <div class="flex items-center space-x-2 overflow-x-auto pb-1 no-scrollbar text-xs">
        <button type="button" onclick="filterCategory('all', this)"
          class="category-pill px-4 py-1.5 rounded-xl font-bold bg-kmi-600 text-white shrink-0 transition-colors">
          Semua Menu
        </button>
        @foreach($categories as $cat)
          <button type="button" onclick="filterCategory('{{ $cat->intCategory_ID }}', this)"
            class="category-pill px-4 py-1.5 rounded-xl font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 shrink-0 transition-colors">
            {{ $cat->txtCategoryName }}
          </button>
        @endforeach
      </div>
    </div>

    <!-- Product Grid -->
    <div id="productGrid" class="flex-1 overflow-y-auto pt-3 grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-3 2xl:grid-cols-4 gap-3.5 content-start auto-rows-max">
      @forelse($products as $prod)
        <div class="product-card bg-white hover:bg-emerald-50/20 border border-slate-200/90 hover:border-kmi-600 rounded-2xl p-3 flex flex-col justify-between cursor-pointer transition-all hover:shadow-md group h-[225px]"
          data-id="{{ $prod->intProduct_ID }}"
          data-name="{{ $prod->txtProductName }}"
          data-price="{{ $prod->floatPrice }}"
          data-stock="{{ $prod->intStock }}"
          data-category="{{ $prod->intCategory_ID }}"
          onclick="addToCart({{ $prod->intProduct_ID }}, '{{ addslashes($prod->txtProductName) }}', {{ $prod->floatPrice }}, {{ $prod->intStock }})">
          
          <div>
            <div class="w-full h-28 rounded-xl bg-slate-100 overflow-hidden relative mb-2 flex items-center justify-center">
              @if($prod->txtProductImage)
                <img src="{{ asset('storage/' . $prod->txtProductImage) }}" alt="{{ $prod->txtProductName }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" onerror="this.src='/images/menu/rice-bowl.webp'">
              @else
                <div class="w-full h-full bg-gradient-to-br from-emerald-50 to-slate-100 flex items-center justify-center text-kmi-600">
                  <i class='bx bx-dish text-3xl'></i>
                </div>
              @endif
              <span class="absolute top-1.5 right-1.5 px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-white/95 backdrop-blur-sm text-slate-700 shadow-sm border border-slate-100">
                Stok: {{ $prod->intStock }}
              </span>
            </div>

            <h4 class="text-xs font-bold text-slate-900 group-hover:text-kmi-700 line-clamp-2 leading-tight">
              {{ $prod->txtProductName }}
            </h4>
          </div>

          <div class="mt-2 flex items-center justify-between pt-2 border-t border-slate-100">
            <span class="text-xs sm:text-sm font-extrabold text-emerald-800">
              Rp {{ number_format($prod->floatPrice, 0, ',', '.') }}
            </span>
            <span class="w-7 h-7 rounded-lg bg-slate-100 border border-slate-200 group-hover:bg-kmi-600 group-hover:text-white group-hover:border-kmi-600 flex items-center justify-center text-slate-500 text-xs transition-colors shadow-sm">
              <i class='bx bx-plus text-sm'></i>
            </span>
          </div>
        </div>
      @empty
        <div class="col-span-full py-16 text-center text-slate-400">
          <i class='bx bx-package text-4xl mb-2 text-slate-300'></i>
          <p class="text-xs">Belum ada produk. Tambahkan menu di menu Produk & Stok.</p>
        </div>
      @endforelse
    </div>

  </div>

  <!-- Right: Cart & Smart Voucher Checkout Section -->
  <div class="w-full lg:w-96 flex flex-col bg-white rounded-3xl p-5 border border-slate-100 shadow-sm overflow-hidden">
    
    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
      <div class="flex items-center space-x-2">
        <i class='bx bx-cart text-xl text-kmi-600'></i>
        <h3 class="text-sm font-extrabold text-slate-900">Keranjang Belanja</h3>
      </div>
      <button type="button" onclick="clearCart()" class="text-[11px] text-slate-400 hover:text-rose-600 font-semibold">
        Kosongkan
      </button>
    </div>

    <!-- Cart Items Scrollable Container -->
    <div class="flex-1 overflow-y-auto py-2">
      <div id="cartEmptyNotice" class="py-12 text-center text-slate-400 text-xs">
        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mx-auto mb-2">
          <i class='bx bx-shopping-bag'></i>
        </div>
        <p class="font-bold text-slate-600">Keranjang masih kosong</p>
        <p class="text-[10px] text-slate-400 mt-0.5">Pilih produk di samping untuk memulai transaksi</p>
      </div>

      <div id="cartItemsList" class="hidden space-y-2"></div>
    </div>

    <!-- Voucher & Calculation Section -->
    <div class="pt-3 border-t border-slate-100 space-y-3">
      
      <!-- Subtotal Display -->
      <div class="flex items-center justify-between text-xs">
        <span class="font-bold text-slate-600">Total Belanja:</span>
        <span id="displaySubtotal" class="font-extrabold text-base text-slate-900">Rp 0</span>
      </div>

      <!-- Voucher Scan Section (Pure Scan Only - No Code Display) -->
      <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200/80 space-y-2">
        
        <!-- State 1: No Voucher Attached Yet -->
        <div id="voucherScanPrompt" class="p-3 bg-gradient-to-r from-emerald-50/70 to-teal-50/70 border-2 border-dashed border-emerald-200 rounded-2xl text-center space-y-2">
          <div class="flex items-center justify-center space-x-1.5 text-xs font-bold text-slate-800">
            <i class='bx bx-qr-scan text-lg text-kmi-600'></i>
            <span>Voucher Digital Karyawan</span>
          </div>
          <p class="text-[11px] text-slate-500">
            Scan QR kartu karyawan via kamera atau scanner booth
          </p>
          <button type="button" onclick="startCameraScanner()"
            class="w-full py-2.5 bg-kmi-600 hover:bg-kmi-700 text-white font-extrabold rounded-xl text-xs flex items-center justify-center space-x-1.5 shadow-md shadow-kmi-600/20 transition-all">
            <i class='bx bx-camera text-base'></i>
            <span>Scan QR Kartu Karyawan</span>
          </button>
        </div>

        <!-- State 2: Voucher Scanned & Active (Code is hidden behind the scenes) -->
        <div id="voucherActiveCard" class="hidden p-3 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs space-y-2">
          <div class="flex items-start justify-between">
            <div class="flex items-center space-x-2">
              <span class="w-7 h-7 rounded-full bg-emerald-200 text-emerald-900 flex items-center justify-center text-sm font-bold">
                <i class='bx bx-check'></i>
              </span>
              <div>
                <p id="vCardEmpName" class="font-extrabold text-slate-900 text-xs leading-tight"></p>
                <p id="vCardEmpNik" class="text-[10px] text-emerald-800 font-semibold"></p>
              </div>
            </div>
            <button type="button" onclick="removeVoucher()" class="text-[10px] text-rose-600 hover:text-rose-800 font-bold px-2 py-1 bg-white rounded-lg border border-rose-200 shadow-sm flex items-center space-x-0.5">
              <i class='bx bx-x text-sm'></i>
              <span>Ganti</span>
            </button>
          </div>

          <div class="pt-1.5 border-t border-emerald-200/80 flex justify-between items-center">
            <span class="text-slate-600 font-medium">Sisa Saldo Voucher:</span>
            <span id="vCardBalance" class="text-emerald-900 font-extrabold text-sm"></span>
          </div>
        </div>

        <!-- Error Alert if scan invalid -->
        <div id="voucherErrorBox" class="hidden p-2.5 rounded-xl border bg-rose-50 border-rose-200 text-xs text-rose-800 flex items-center justify-between">
          <div class="flex items-center space-x-1.5">
            <i class='bx bx-error-circle text-base text-rose-600 shrink-0'></i>
            <span id="voucherErrorText"></span>
          </div>
          <button type="button" onclick="dismissVoucherError()" class="text-rose-400 hover:text-rose-700">
            <i class='bx bx-x text-base'></i>
          </button>
        </div>

      </div>

      <!-- Payment Breakdown Card -->
      <div id="paymentBreakdownBox" class="hidden bg-emerald-50/60 p-3 rounded-2xl border border-emerald-200 text-xs space-y-1.5">
        <div class="flex justify-between items-center text-emerald-950 font-bold">
          <span>Dibayar Voucher:</span>
          <span id="displayVoucherCut" class="text-sky-800 font-extrabold">- Rp 0</span>
        </div>
        <div class="flex justify-between items-center text-emerald-950 font-bold">
          <span>Kekurangan Tunai (Cash):</span>
          <span id="displayCashOwed" class="text-amber-800 font-extrabold">Rp 0</span>
        </div>
      </div>

      <!-- Cash Given Input (when cash owed or pure cash) -->
      <div id="cashInputContainer" class="space-y-1.5">
        <div class="flex justify-between text-[11px] font-bold text-slate-700">
          <span>Uang Tunai Diterima:</span>
          <span id="displayChange" class="text-emerald-700 font-bold">Kembalian: Rp 0</span>
        </div>
        <div class="relative">
          <span class="absolute left-3 top-2 text-xs font-bold text-slate-400">Rp</span>
          <input type="text" inputmode="numeric" id="cashGivenInput" oninput="calculateChange()" placeholder="0"
            class="number-separator w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono font-bold text-slate-900 focus:outline-none focus:border-kmi-600 focus:bg-white">
        </div>
        
        <!-- Quick Cash Suggestions -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-0.5 no-scrollbar text-[10px]">
          <button type="button" onclick="setExactCash()" class="px-2 py-1 bg-emerald-50 hover:bg-emerald-100 text-kmi-800 font-bold rounded-lg border border-emerald-200/60 shrink-0">
            Uang Pas
          </button>
          <button type="button" onclick="addQuickCash(10000)" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg shrink-0">
            +10.000
          </button>
          <button type="button" onclick="addQuickCash(20000)" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg shrink-0">
            +20.000
          </button>
          <button type="button" onclick="addQuickCash(50000)" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg shrink-0">
            +50.000
          </button>
          <button type="button" onclick="addQuickCash(100000)" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg shrink-0">
            +100.000
          </button>
        </div>
      </div>

      <!-- Checkout Button -->
      <button type="button" id="btnCheckout" onclick="processCheckout()" disabled
        class="w-full py-3.5 bg-slate-300 text-slate-500 font-extrabold rounded-2xl text-xs uppercase tracking-wider transition-all flex items-center justify-center space-x-2">
        <i class='bx bx-check-shield text-lg'></i>
        <span>Selesaikan Transaksi</span>
      </button>

    </div>

  </div>

</div>

<!-- Camera QR & Barcode Scanner Modal -->
<div id="cameraModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl relative">
    <button type="button" onclick="stopCameraScanner()" class="absolute top-5 right-5 text-slate-400 hover:text-slate-700">
      <i class='bx bx-x text-2xl'></i>
    </button>
    
    <div class="flex items-center space-x-2 mb-4">
      <i class='bx bx-camera text-xl text-kmi-600'></i>
      <h3 class="text-sm font-extrabold text-slate-900">Arahkan Kamera ke Barcode Karyawan</h3>
    </div>

    <div id="scannerView" class="w-full h-64 bg-black rounded-2xl overflow-hidden border border-slate-200"></div>

    <p class="text-[11px] text-slate-400 text-center mt-3">
      Posisikan barcode kartu voucher atau QR code tepat di tengah kotak kamera
    </p>
  </div>
</div>

<!-- Receipt Modal on Success -->
<div id="receiptModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl relative text-center">
    <div class="w-12 h-12 rounded-full bg-emerald-100 text-kmi-600 flex items-center justify-center text-2xl mx-auto mb-3">
      <i class='bx bx-check'></i>
    </div>
    <h3 class="text-base font-extrabold text-slate-900">Transaksi Berhasil!</h3>
    <p id="receiptInv" class="text-xs font-mono text-slate-500 mt-0.5"></p>

    <!-- Receipt Details -->
    <div class="my-4 p-4 rounded-2xl bg-slate-50 border border-slate-200 text-left text-xs space-y-2">
      <div class="flex justify-between">
        <span class="text-slate-500">Pelanggan:</span>
        <span id="receiptCustomer" class="font-bold text-slate-900"></span>
      </div>
      <div class="flex justify-between">
        <span class="text-slate-500">Metode Bayar:</span>
        <span id="receiptMethod" class="font-bold text-slate-900"></span>
      </div>
      <div class="border-t border-slate-200 pt-2 flex justify-between font-extrabold">
        <span>Total Belanja:</span>
        <span id="receiptTotal"></span>
      </div>
      <div class="flex justify-between text-sky-800">
        <span>Voucher Terpotong:</span>
        <span id="receiptVoucherCut"></span>
      </div>
      <div class="flex justify-between text-amber-800">
        <span>Tunai Dibayar:</span>
        <span id="receiptCashPaid"></span>
      </div>
      <div class="flex justify-between text-emerald-800">
        <span>Kembalian:</span>
        <span id="receiptChange"></span>
      </div>
      <div id="receiptVoucherBalanceRow" class="border-t border-slate-200 pt-2 flex justify-between text-slate-600">
        <span>Sisa Saldo Voucher:</span>
        <span id="receiptVoucherBalance" class="font-bold text-slate-900"></span>
      </div>
    </div>

    <div class="flex space-x-2">
      <a id="btnReceiptPrint" href="#" target="_blank" class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-xl text-xs flex items-center justify-center space-x-1">
        <i class='bx bx-printer text-base'></i>
        <span>Cetak Nota</span>
      </a>
      <button type="button" onclick="closeReceiptModal()" class="flex-1 py-2.5 bg-kmi-600 hover:bg-kmi-700 text-white font-bold rounded-xl text-xs">
        Selesai / Lanjut
      </button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  let cart = [];
  let currentVoucher = null;
  let html5Scanner = null;

  function filterCategory(catId, btn) {
    document.querySelectorAll('.category-pill').forEach(el => {
      el.classList.remove('bg-kmi-600', 'text-white');
      el.classList.add('bg-slate-100', 'text-slate-700');
    });
    btn.classList.add('bg-kmi-600', 'text-white');
    btn.classList.remove('bg-slate-100', 'text-slate-700');

    document.querySelectorAll('.product-card').forEach(card => {
      const matchCat = catId === 'all' || card.getAttribute('data-category') === String(catId);
      card.style.display = matchCat ? 'flex' : 'none';
    });
  }

  function filterProducts() {
    const q = document.getElementById('productSearchInput').value.toLowerCase();
    document.querySelectorAll('.product-card').forEach(card => {
      const name = card.getAttribute('data-name').toLowerCase();
      card.style.display = name.includes(q) ? 'flex' : 'none';
    });
  }

  function addToCart(id, name, price, maxStock) {
    const existing = cart.find(item => item.id === id);
    if (existing) {
      if (maxStock > 0 && existing.quantity >= maxStock) {
        alert(`Maksimal stok tercapai untuk '${name}' (${maxStock}).`);
        return;
      }
      existing.quantity++;
    } else {
      cart.push({ id, name, price, quantity: 1, maxStock });
    }
    renderCart();
  }

  function updateQty(id, delta) {
    const item = cart.find(i => i.id === id);
    if (!item) return;

    item.quantity += delta;
    if (item.quantity <= 0) {
      cart = cart.filter(i => i.id !== id);
    } else if (item.maxStock > 0 && item.quantity > item.maxStock) {
      item.quantity = item.maxStock;
      alert(`Maksimal stok produk ini hanya ${item.maxStock}.`);
    }
    renderCart();
  }

  function clearCart() {
    cart = [];
    removeVoucher();
    renderCart();
  }

  function removeVoucher() {
    currentVoucher = null;
    document.getElementById('voucherActiveCard')?.classList.add('hidden');
    document.getElementById('voucherScanPrompt')?.classList.remove('hidden');
    document.getElementById('voucherErrorBox')?.classList.add('hidden');
    recalculatePayment();
  }

  function dismissVoucherError() {
    document.getElementById('voucherErrorBox')?.classList.add('hidden');
  }

  function getSubtotal() {
    return cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
  }

  function renderCart() {
    const list = document.getElementById('cartItemsList');
    const emptyNotice = document.getElementById('cartEmptyNotice');
    const subtotal = getSubtotal();

    if (cart.length === 0) {
      if (emptyNotice) emptyNotice.classList.remove('hidden');
      if (list) {
        list.classList.add('hidden');
        list.innerHTML = '';
      }
      document.getElementById('displaySubtotal').innerText = 'Rp 0';
      document.getElementById('paymentBreakdownBox')?.classList.add('hidden');
      const btn = document.getElementById('btnCheckout');
      if (btn) {
        btn.disabled = true;
        btn.classList.add('bg-slate-300', 'text-slate-500');
        btn.classList.remove('bg-kmi-600', 'text-white', 'hover:bg-kmi-700', 'shadow-lg');
      }
      return;
    }

    if (emptyNotice) emptyNotice.classList.add('hidden');
    if (list) {
      list.classList.remove('hidden');
      list.innerHTML = cart.map(item => `
        <div class="flex items-center justify-between p-2.5 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs hover:border-slate-300 transition-colors">
          <div class="min-w-0 flex-1 pr-2">
            <p class="font-bold text-slate-900 truncate leading-tight">${item.name}</p>
            <p class="text-[11px] text-emerald-800 font-semibold mt-0.5">Rp ${Number(item.price).toLocaleString('id-ID')}</p>
          </div>
          <div class="flex items-center space-x-2">
            <button type="button" onclick="updateQty(${item.id}, -1)" class="w-6 h-6 rounded-lg bg-white border border-slate-200 text-slate-700 font-extrabold flex items-center justify-center hover:bg-slate-100 active:scale-95 transition-all">-</button>
            <span class="font-extrabold text-xs w-4 text-center text-slate-800">${item.quantity}</span>
            <button type="button" onclick="updateQty(${item.id}, 1)" class="w-6 h-6 rounded-lg bg-white border border-slate-200 text-slate-700 font-extrabold flex items-center justify-center hover:bg-slate-100 active:scale-95 transition-all">+</button>
            <span class="font-extrabold text-xs text-slate-900 w-16 text-right">Rp ${Number(item.price * item.quantity).toLocaleString('id-ID')}</span>
          </div>
        </div>
      `).join('');
    }

    document.getElementById('displaySubtotal').innerText = 'Rp ' + Number(subtotal).toLocaleString('id-ID');

    // Calculate voucher vs cash
    recalculatePayment();
  }

  function recalculatePayment() {
    const breakdownBox = document.getElementById('paymentBreakdownBox');
    const btn = document.getElementById('btnCheckout');
    const displayChange = document.getElementById('displayChange');
    const subtotal = getSubtotal();

    if (subtotal <= 0) {
      if (breakdownBox) breakdownBox.classList.add('hidden');
      if (displayChange) displayChange.innerText = 'Kembalian: Rp 0';
      if (btn) {
        btn.disabled = true;
        btn.classList.add('bg-slate-300', 'text-slate-500');
        btn.classList.remove('bg-kmi-600', 'text-white', 'hover:bg-kmi-700', 'shadow-lg');
      }
      return;
    }

    let voucherCut = 0;
    let cashOwed = subtotal;

    if (currentVoucher && currentVoucher.valid) {
      if (breakdownBox) breakdownBox.classList.remove('hidden');
      const vBalance = Number(currentVoucher.current_balance);

      if (vBalance >= subtotal) {
        voucherCut = subtotal;
        cashOwed = 0;
      } else {
        voucherCut = vBalance;
        cashOwed = subtotal - vBalance;
      }

      const elCut = document.getElementById('displayVoucherCut');
      const elOwed = document.getElementById('displayCashOwed');
      if (elCut) elCut.innerText = '- Rp ' + Number(voucherCut).toLocaleString('id-ID');
      if (elOwed) elOwed.innerText = 'Rp ' + Number(cashOwed).toLocaleString('id-ID');
    } else {
      if (breakdownBox) breakdownBox.classList.add('hidden');
    }

    // Check if ready to checkout
    const cashGivenInput = document.getElementById('cashGivenInput');
    const cashGiven = cashGivenInput ? (parseRawNumber(cashGivenInput.value) || 0) : 0;
    const change = Math.max(0, cashGiven - cashOwed);
    if (displayChange) {
      displayChange.innerText = 'Kembalian: Rp ' + Number(change).toLocaleString('id-ID');
    }

    if (btn) {
      if (cashOwed === 0 || cashGiven >= cashOwed) {
        btn.disabled = false;
        btn.classList.remove('bg-slate-300', 'text-slate-500');
        btn.classList.add('bg-kmi-600', 'text-white', 'hover:bg-kmi-700', 'shadow-lg');
      } else {
        btn.disabled = true;
        btn.classList.add('bg-slate-300', 'text-slate-500');
        btn.classList.remove('bg-kmi-600', 'text-white', 'hover:bg-kmi-700', 'shadow-lg');
      }
    }
  }

  function getCashOwed() {
    const subtotal = getSubtotal();
    if (subtotal <= 0) return 0;
    if (currentVoucher && currentVoucher.valid) {
      const vBalance = Number(currentVoucher.current_balance);
      return Math.max(0, subtotal - vBalance);
    }
    return subtotal;
  }

  function setExactCash() {
    const owed = getCashOwed();
    const input = document.getElementById('cashGivenInput');
    if (!input) return;
    input.value = owed > 0 ? formatNumberWithSeparator(owed) : '0';
    calculateChange();
  }

  function addQuickCash(amount) {
    const input = document.getElementById('cashGivenInput');
    if (!input) return;
    const current = parseRawNumber(input.value);
    const updated = current + amount;
    input.value = formatNumberWithSeparator(updated);
    calculateChange();
  }

  function calculateChange() {
    recalculatePayment();
  }

  async function checkVoucherCode(code) {
    if (!code || !code.trim()) {
      return;
    }

    dismissVoucherError();

    try {
      const res = await fetch("{{ route('umkm.pos.check-voucher') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json',
        },
        body: JSON.stringify({ code: code.trim() }),
      });
      const data = await res.json();

      if (data.valid) {
        currentVoucher = data; // Code is kept in memory behind the scenes
        document.getElementById('vCardEmpName').innerText = data.employee_name;
        document.getElementById('vCardEmpNik').innerText = 'NIK: ' + data.employee_nik + ' • Status: ' + (data.status || 'Aktif');
        document.getElementById('vCardBalance').innerText = 'Rp ' + Number(data.current_balance).toLocaleString('id-ID');

        document.getElementById('voucherScanPrompt').classList.add('hidden');
        document.getElementById('voucherActiveCard').classList.remove('hidden');
        document.getElementById('voucherErrorBox').classList.add('hidden');
      } else {
        removeVoucher();
        document.getElementById('voucherErrorText').innerText = data.message || 'Kartu voucher tidak valid atau belum aktif.';
        document.getElementById('voucherErrorBox').classList.remove('hidden');
      }
      recalculatePayment();
    } catch (e) {
      removeVoucher();
      document.getElementById('voucherErrorText').innerText = 'Gagal menghubungi server verifikasi.';
      document.getElementById('voucherErrorBox').classList.remove('hidden');
    }
  }

  // Hardware Barcode / QR Scanner Gun Listener (Keyboard Wedge)
  let barcodeBuffer = '';
  let barcodeLastTime = 0;

  document.addEventListener('keydown', (e) => {
    // Ignore keystrokes when cashier is typing in search or cash inputs
    const activeId = document.activeElement ? document.activeElement.id : '';
    if (activeId === 'productSearchInput' || activeId === 'cashGivenInput') {
      return;
    }

    const now = Date.now();
    if (now - barcodeLastTime > 150) {
      barcodeBuffer = '';
    }
    barcodeLastTime = now;

    if (e.key === 'Enter') {
      if (barcodeBuffer.trim().length >= 5) {
        e.preventDefault();
        const codeScanned = barcodeBuffer.trim();
        barcodeBuffer = '';
        checkVoucherCode(codeScanned);
      }
    } else if (e.key.length === 1) {
      barcodeBuffer += e.key;
    }
  });
  // Camera Scanner with html5-qrcode
  function startCameraScanner() {
    document.getElementById('cameraModal').classList.remove('hidden');
    html5Scanner = new Html5Qrcode("scannerView");
    html5Scanner.start(
      { facingMode: "environment" },
      { fps: 10, qrbox: { width: 250, height: 250 } },
      (decodedText) => {
        stopCameraScanner();
        checkVoucherCode(decodedText);
      },
      (err) => {}
    ).catch(err => {
      alert("Tidak dapat mengakses kamera: " + err);
      stopCameraScanner();
    });
  }

  function stopCameraScanner() {
    if (html5Scanner) {
      html5Scanner.stop().then(() => {
        html5Scanner.clear();
        html5Scanner = null;
      }).catch(() => {});
    }
    document.getElementById('cameraModal').classList.add('hidden');
  }

  // Process Checkout
  async function processCheckout() {
    if (cart.length === 0) return;

    const btn = document.getElementById('btnCheckout');
    btn.disabled = true;
    btn.innerHTML = "<i class='bx bx-loader-alt animate-spin text-lg'></i> Memproses Transaksi...";

    const payload = {
      items: cart.map(i => ({ id: i.id, quantity: i.quantity })),
      voucher_code: currentVoucher ? currentVoucher.voucher_code : null,
      cash_given: parseRawNumber(document.getElementById('cashGivenInput')?.value) || 0,
    };

    try {
      const res = await fetch("{{ route('umkm.pos.checkout') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json',
        },
        body: JSON.stringify(payload),
      });

      const data = await res.json();
      if (!res.ok || !data.success) {
        throw new Error(data.message || 'Gagal memproses transaksi.');
      }

      // Success modal
      const sale = data.sale;
      document.getElementById('receiptInv').innerText = sale.invoice_number + ' • ' + sale.time;
      document.getElementById('receiptCustomer').innerText = sale.employee_name;
      document.getElementById('receiptMethod').innerText = sale.payment_type;
      document.getElementById('receiptTotal').innerText = 'Rp ' + Number(sale.total_amount).toLocaleString('id-ID');
      document.getElementById('receiptVoucherCut').innerText = 'Rp ' + Number(sale.voucher_amount).toLocaleString('id-ID');
      document.getElementById('receiptCashPaid').innerText = 'Rp ' + Number(sale.cash_amount).toLocaleString('id-ID');
      document.getElementById('receiptChange').innerText = 'Rp ' + Number(sale.change_amount).toLocaleString('id-ID');

      if (sale.voucher_remaining !== null) {
        document.getElementById('receiptVoucherBalanceRow').classList.remove('hidden');
        document.getElementById('receiptVoucherBalance').innerText = 'Rp ' + Number(sale.voucher_remaining).toLocaleString('id-ID');
      } else {
        document.getElementById('receiptVoucherBalanceRow').classList.add('hidden');
      }

      document.getElementById('btnReceiptPrint').href = `/umkm/pos/receipt/${sale.id}`;
      document.getElementById('receiptModal').classList.remove('hidden');

      // Clear Cart
      clearCart();
      document.getElementById('cashGivenInput').value = '';
    } catch (e) {
      alert("Error: " + e.message);
    } finally {
      btn.innerHTML = "<i class='bx bx-check-shield text-lg'></i> <span>Selesaikan Transaksi</span>";
      recalculatePayment();
    }
  }

  function closeReceiptModal() {
    document.getElementById('receiptModal').classList.add('hidden');
  }
</script>
@endpush

