@extends('layouts.app')

@section('header_title', 'Manajemen Menu & Stok Produk - ' . ($merchant->txtMerchantName ?? 'UMKM'))

@section('content')
<div class="space-y-6">

  <!-- Header & Add Button -->
  <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-xl font-extrabold text-slate-900">Katalog Menu & Manajemen Stok</h1>
      <p class="text-xs text-slate-500 mt-1">
        Kelola varian makanan, minuman, harga, serta ketersediaan stok di booth {{ $merchant->txtBoothNumber }}.
      </p>
    </div>

    <button type="button" onclick="openModal('modalAddProduct')" class="px-4 py-2.5 bg-kmi-600 hover:bg-kmi-700 text-white font-bold rounded-xl text-xs shadow-md shadow-kmi-600/30 flex items-center space-x-2">
      <i class='bx bx-plus-circle text-base'></i>
      <span>+ Tambah Menu Baru</span>
    </button>
  </div>

  <!-- Search & Category Filters -->
  <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
    <form action="{{ route('umkm.products.index') }}" method="GET" class="flex-1 flex items-center gap-2 w-full">
      <div class="relative flex-1">
        <i class='bx bx-search absolute left-3 top-2.5 text-slate-400 text-lg'></i>
        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama menu..."
          class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-kmi-600 focus:bg-white">
      </div>

      <select name="category_id" class="py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700">
        <option value="">Semua Kategori</option>
        @foreach($categories as $cat)
          <option value="{{ $cat->intCategory_ID }}" {{ $categoryId == $cat->intCategory_ID ? 'selected' : '' }}>
            {{ $cat->txtCategoryName }}
          </option>
        @endforeach
      </select>

      <button type="submit" class="px-4 py-2 bg-slate-800 text-white font-bold rounded-xl text-xs">Filter</button>
      @if($search || $categoryId)
        <a href="{{ route('umkm.products.index') }}" class="p-2 text-slate-400 hover:text-rose-600 text-xs">Reset</a>
      @endif
    </form>
  </div>

  <!-- Products Grid -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    @forelse($products as $prod)
      <div class="bg-white rounded-3xl p-4 border border-slate-100 shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow">
        <div>
          <div class="w-full h-36 rounded-2xl bg-slate-100 overflow-hidden relative mb-3 flex items-center justify-center">
            @if($prod->txtProductImage)
              <img src="{{ asset('storage/' . $prod->txtProductImage) }}" alt="{{ $prod->txtProductName }}" class="w-full h-full object-cover" onerror="this.src='/images/menu/rice-bowl.webp'">
            @else
              <div class="w-full h-full bg-gradient-to-br from-emerald-100 to-slate-200 flex items-center justify-center text-kmi-700">
                <i class='bx bx-dish text-4xl'></i>
              </div>
            @endif

            <span class="absolute top-2 right-2 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold {{ $prod->intStock > 10 ? 'bg-emerald-600 text-white' : ($prod->intStock > 0 ? 'bg-amber-500 text-white' : 'bg-rose-600 text-white') }} shadow-sm">
              Stok: {{ $prod->intStock }} {{ $prod->txtUnit }}
            </span>
          </div>

          <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
            {{ $prod->category?->txtCategoryName ?? 'Kategori Umum' }}
          </div>
          <h3 class="text-sm font-bold text-slate-900 mt-0.5 line-clamp-1">{{ $prod->txtProductName }}</h3>
          <p class="text-xs font-extrabold text-emerald-800 mt-1">
            Rp {{ number_format($prod->floatPrice, 0, ',', '.') }}
          </p>
          @if($prod->txtDescription)
            <p class="text-[11px] text-slate-500 mt-1 line-clamp-2">{{ $prod->txtDescription }}</p>
          @endif
        </div>

        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
          <!-- Quick Stock Adjust Form -->
          <form action="{{ route('umkm.products.stock', $prod->intProduct_ID) }}" method="POST" class="flex items-center space-x-1">
            @csrf
            <span class="text-[10px] text-slate-400 font-bold">Ubah:</span>
            <input type="number" name="stock" value="{{ $prod->intStock }}" min="0" class="w-14 px-2 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs font-mono font-bold text-center">
            <button type="submit" class="p-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs" title="Simpan Stok">
              <i class='bx bx-check text-sm'></i>
            </button>
          </form>

          <div class="flex items-center space-x-1">
            <button type="button" onclick="editProduct({{ json_encode($prod) }})" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs" title="Edit">
              <i class='bx bx-edit text-base'></i>
            </button>
            <form action="{{ route('umkm.products.destroy', $prod->intProduct_ID) }}" method="POST" onsubmit="return confirm('Hapus menu ini?')">
              @csrf
              @method('DELETE')
              <button type="submit" class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs" title="Hapus">
                <i class='bx bx-trash text-base'></i>
              </button>
            </form>
          </div>
        </div>
      </div>
    @empty
      <div class="col-span-full py-16 text-center text-slate-400">
        Belum ada produk di booth ini. Klik tombol tambah menu di atas.
      </div>
    @endforelse
  </div>

  @if($products->hasPages())
    <div class="p-4 bg-white rounded-2xl border border-slate-100">
      {{ $products->links() }}
    </div>
  @endif

</div>

<!-- Modal Add Product -->
<div id="modalAddProduct" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl relative max-h-[90vh] overflow-y-auto">
    <button type="button" onclick="closeModal('modalAddProduct')" class="absolute top-5 right-5 text-slate-400 hover:text-slate-700">
      <i class='bx bx-x text-2xl'></i>
    </button>

    <h3 class="text-base font-extrabold text-slate-900 mb-1">Tambah Menu Produk Baru</h3>
    <p class="text-xs text-slate-500 mb-5">Daftarkan menu makanan/minuman yang dijual di stand Anda</p>

    <form action="{{ route('umkm.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
      @csrf

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Produk *</label>
        <input type="text" name="product_name" required placeholder="Contoh: Nasi Liwet Ayam Komplit" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-kmi-600 focus:bg-white">
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Kategori</label>
          <select name="category_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
            <option value="">Pilih Kategori...</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->intCategory_ID }}">{{ $cat->txtCategoryName }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Atau Kategori Baru</label>
          <input type="text" name="new_category" placeholder="Buat kategori baru..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
        </div>
      </div>

      <div class="grid grid-cols-3 gap-3">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Harga (Rp) *</label>
          <div class="relative">
            <span class="absolute left-3 top-2 text-xs font-bold text-slate-400">Rp</span>
            <input type="text" inputmode="numeric" name="price" required placeholder="25.000" class="number-separator w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:outline-none focus:border-kmi-600 focus:bg-white">
          </div>
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Stok Awal *</label>
          <input type="text" inputmode="numeric" name="stock" required value="50" class="number-separator w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:outline-none focus:border-kmi-600 focus:bg-white">
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Satuan</label>
          <input type="text" name="unit" value="Porsi" placeholder="Porsi/Cup/Pcs" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-kmi-600 focus:bg-white">
        </div>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Foto Produk (Opsional)</label>
        <input type="file" name="image" accept="image/*" class="text-xs text-slate-600 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-kmi-600 file:text-white hover:file:bg-kmi-700 cursor-pointer">
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan / Deskripsi</label>
        <textarea name="description" rows="2" placeholder="Komposisi atau catatan menu..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs"></textarea>
      </div>

      <div class="pt-2 flex justify-end space-x-2">
        <button type="button" onclick="closeModal('modalAddProduct')" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl text-xs">Batal</button>
        <button type="submit" class="px-5 py-2 bg-kmi-600 hover:bg-kmi-700 text-white font-bold rounded-xl text-xs shadow-md shadow-kmi-600/30">Simpan Menu</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Product -->
<div id="modalEditProduct" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl relative max-h-[90vh] overflow-y-auto">
    <button type="button" onclick="closeModal('modalEditProduct')" class="absolute top-5 right-5 text-slate-400 hover:text-slate-700">
      <i class='bx bx-x text-2xl'></i>
    </button>

    <h3 class="text-base font-extrabold text-slate-900 mb-1">Edit Menu Produk</h3>
    <p class="text-xs text-slate-500 mb-5">Perbarui harga, ketersediaan stok, atau foto</p>

    <form id="editProductForm" method="POST" enctype="multipart/form-data" class="space-y-4">
      @csrf
      @method('PUT')

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Produk *</label>
        <input type="text" name="product_name" id="edit_product_name" required class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Kategori</label>
          <select name="category_id" id="edit_category_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
            <option value="">Pilih Kategori...</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->intCategory_ID }}">{{ $cat->txtCategoryName }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Satuan</label>
          <input type="text" name="unit" id="edit_unit" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
        </div>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Harga (Rp) *</label>
          <div class="relative">
            <span class="absolute left-3 top-2 text-xs font-bold text-slate-400">Rp</span>
            <input type="text" inputmode="numeric" name="price" id="edit_price" required class="number-separator w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:outline-none focus:border-kmi-600 focus:bg-white">
          </div>
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Stok *</label>
          <input type="text" inputmode="numeric" name="stock" id="edit_stock" required class="number-separator w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:outline-none focus:border-kmi-600 focus:bg-white">
        </div>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Ganti Foto Produk (Opsional)</label>
        <input type="file" name="image" accept="image/*" class="text-xs text-slate-600 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-kmi-600 file:text-white hover:file:bg-kmi-700 cursor-pointer">
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan / Deskripsi</label>
        <textarea name="description" id="edit_description" rows="2" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs"></textarea>
      </div>

      <div class="pt-2 flex justify-end space-x-2">
        <button type="button" onclick="closeModal('modalEditProduct')" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl text-xs">Batal</button>
        <button type="submit" class="px-5 py-2 bg-kmi-600 hover:bg-kmi-700 text-white font-bold rounded-xl text-xs shadow-md shadow-kmi-600/30">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<script>
  function openModal(id) { document.getElementById(id)?.classList.remove('hidden'); }
  function closeModal(id) { document.getElementById(id)?.classList.add('hidden'); }

  function editProduct(p) {
    const form = document.getElementById('editProductForm');
    form.action = `/umkm/products/${p.intProduct_ID}`;
    document.getElementById('edit_product_name').value = p.txtProductName || '';
    document.getElementById('edit_category_id').value = p.intCategory_ID || '';
    document.getElementById('edit_price').value = formatNumberWithSeparator(p.floatPrice || 0);
    document.getElementById('edit_stock').value = formatNumberWithSeparator(p.intStock || 0);
    document.getElementById('edit_unit').value = p.txtUnit || 'Pcs';
    document.getElementById('edit_description').value = p.txtDescription || '';
    openModal('modalEditProduct');
  }
</script>
@endsection

