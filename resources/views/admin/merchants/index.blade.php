@extends('layouts.app')

@section('header_title', 'Mitra UMKM Binaan - Hoya-Hoya Day Festival')

@section('content')
<div class="space-y-6">

  <!-- Header & Add Button -->
  <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-xl font-extrabold text-slate-900">Manajemen Booth UMKM Festival</h1>
      <p class="text-xs text-slate-500 mt-1">
        Kelola tenant dan UMKM lokal yang diundang untuk berpartisipasi pada Hoya-Hoya Day Festival.
      </p>
    </div>

    <button type="button" onclick="openModal('modalAddMerchant')" class="px-4 py-2.5 bg-kmi-600 hover:bg-kmi-700 text-white font-bold rounded-xl text-xs shadow-md shadow-kmi-600/30 flex items-center space-x-2">
      <i class='bx bx-plus-circle text-base'></i>
      <span>+ Daftarkan Booth UMKM</span>
    </button>
  </div>

  <!-- Search Bar -->
  <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm">
    <form action="{{ route('admin.merchants.index') }}" method="GET" class="flex items-center gap-2">
      <div class="relative flex-1">
        <i class='bx bx-search absolute left-3 top-2.5 text-slate-400 text-lg'></i>
        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama toko, nomor stand/booth, atau nama pemilik..."
          class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-kmi-600 focus:bg-white">
      </div>
      <button type="submit" class="px-4 py-2 bg-slate-800 text-white font-bold rounded-xl text-xs">Cari</button>
      @if($search)
        <a href="{{ route('admin.merchants.index') }}" class="p-2 text-slate-400 hover:text-rose-600 text-xs">Reset</a>
      @endif
    </form>
  </div>

  <!-- Merchants Grid/Table -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($merchants as $m)
      <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow">
        <div>
          <div class="flex items-start justify-between">
            <span class="px-2.5 py-1 bg-emerald-100 text-kmi-800 rounded-lg text-xs font-extrabold">
              {{ $m->txtBoothNumber ?? 'Stand' }}
            </span>
            <span class="text-[11px] font-mono text-slate-400">{{ $m->txtMerchantCode }}</span>
          </div>

          <h3 class="text-base font-extrabold text-slate-900 mt-3">{{ $m->txtMerchantName }}</h3>
          <p class="text-xs text-slate-500 mt-0.5">{{ $m->txtCategory ?? 'Kuliner & Makanan' }}</p>

          <div class="mt-4 pt-4 border-t border-slate-100 space-y-2 text-xs">
            <div class="flex items-center text-slate-600 space-x-2">
              <i class='bx bx-user text-slate-400 text-base'></i>
              <span>Pemilik: <b class="text-slate-800">{{ $m->txtOwnerName }}</b></span>
            </div>
            <div class="flex items-center text-slate-600 space-x-2">
              <i class='bx bxl-whatsapp text-emerald-600 text-base'></i>
              <span>{{ $m->txtPhone }}</span>
            </div>
            <div class="flex items-center text-slate-600 space-x-2">
              <i class='bx bx-credit-card text-slate-400 text-base'></i>
              <span>{{ $m->txtBankName ?? 'Bank' }}: <b class="text-slate-800">{{ $m->txtBankAccountNumber ?? '-' }}</b> ({{ $m->txtBankAccountHolder }})</span>
            </div>
            <div class="flex items-center text-slate-600 space-x-2">
              <i class='bx bx-key text-slate-400 text-base'></i>
              <span>Username Login: <code class="font-mono bg-slate-100 px-1 py-0.5 rounded text-slate-800">{{ $m->user?->txtUsername ?? '-' }}</code></span>
            </div>
          </div>
        </div>

        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
          <div class="text-slate-500">
            <span class="font-bold text-slate-800">{{ $m->products_count }}</span> Produk &bull;
            <span class="font-bold text-slate-800">{{ $m->sales_count }}</span> Penjualan
          </div>

          <div class="flex items-center space-x-1">
            <button type="button" onclick="editMerchant({{ json_encode($m) }})" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg" title="Edit Data Booth">
              <i class='bx bx-edit text-base'></i>
            </button>
            <form action="{{ route('admin.merchants.destroy', $m->intMerchant_ID) }}" method="POST" onsubmit="return confirm('Nonaktifkan booth UMKM ini?')">
              @csrf
              @method('DELETE')
              <button type="submit" class="p-2 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg" title="Nonaktifkan">
                <i class='bx bx-trash text-base'></i>
              </button>
            </form>
          </div>
        </div>
      </div>
    @empty
      <div class="col-span-full py-12 text-center text-slate-400">
        Belum ada mitra UMKM terdaftar.
      </div>
    @endforelse
  </div>

  @if($merchants->hasPages())
    <div class="p-4 bg-white rounded-2xl border border-slate-100">
      {{ $merchants->links() }}
    </div>
  @endif

</div>

<!-- Modal Add Merchant -->
<div id="modalAddMerchant" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl relative max-h-[90vh] overflow-y-auto">
    <button type="button" onclick="closeModal('modalAddMerchant')" class="absolute top-5 right-5 text-slate-400 hover:text-slate-700">
      <i class='bx bx-x text-2xl'></i>
    </button>

    <h3 class="text-base font-extrabold text-slate-900 mb-1">Daftarkan Mitra UMKM Baru</h3>
    <p class="text-xs text-slate-500 mb-5">Lengkapi profil stand dan akun kasir untuk UMKM</p>

    <form action="{{ route('admin.merchants.store') }}" method="POST" class="space-y-4">
      @csrf

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Nama Toko / UMKM *</label>
          <input type="text" name="merchant_name" required placeholder="Contoh: Dapur Bu Siti" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Stand / Booth *</label>
          <input type="text" name="booth_number" required placeholder="Contoh: Booth A-01" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
        </div>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Nama Pemilik *</label>
          <input type="text" name="owner_name" required placeholder="Nama lengkap pemilik" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">No WhatsApp *</label>
          <input type="text" name="phone" required placeholder="62812..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
        </div>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Produk</label>
        <input type="text" name="category" placeholder="Makanan Berat, Minuman, Camilan, dll" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
      </div>

      <!-- Bank Details -->
      <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200/80 space-y-3">
        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Rekening Pencairan Dana Klaim:</p>
        <div class="grid grid-cols-3 gap-2">
          <div>
            <label class="block text-[10px] font-bold text-slate-600 mb-1">Bank</label>
            <input type="text" name="bank_name" placeholder="BCA/Mandiri/BRI" class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs">
          </div>
          <div>
            <label class="block text-[10px] font-bold text-slate-600 mb-1">No Rekening</label>
            <input type="text" name="bank_account_number" placeholder="123456789" class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs">
          </div>
          <div>
            <label class="block text-[10px] font-bold text-slate-600 mb-1">Atas Nama</label>
            <input type="text" name="bank_account_holder" placeholder="Nama pemilik rek" class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs">
          </div>
        </div>
      </div>

      <!-- Account Login Info -->
      <div class="p-3.5 bg-emerald-50/50 rounded-2xl border border-emerald-200/80 space-y-3">
        <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-800">Akun Login Kasir POS:</p>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-[10px] font-bold text-slate-700 mb-1">Username Login *</label>
            <input type="text" name="username" required placeholder="umkm_siti" class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs">
          </div>
          <div>
            <label class="block text-[10px] font-bold text-slate-700 mb-1">Password *</label>
            <input type="password" name="password" required placeholder="••••••••" value="password" class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs">
          </div>
        </div>
      </div>

      <div class="pt-2 flex justify-end space-x-2">
        <button type="button" onclick="closeModal('modalAddMerchant')" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl text-xs">Batal</button>
        <button type="submit" class="px-5 py-2 bg-kmi-600 hover:bg-kmi-700 text-white font-bold rounded-xl text-xs shadow-md shadow-kmi-600/30">Daftarkan UMKM</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Merchant -->
<div id="modalEditMerchant" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl relative max-h-[90vh] overflow-y-auto">
    <button type="button" onclick="closeModal('modalEditMerchant')" class="absolute top-5 right-5 text-slate-400 hover:text-slate-700">
      <i class='bx bx-x text-2xl'></i>
    </button>

    <h3 class="text-base font-extrabold text-slate-900 mb-1">Edit Booth UMKM</h3>
    <p class="text-xs text-slate-500 mb-5">Perbarui informasi stand atau rekening transfer</p>

    <form id="editMerchantForm" method="POST" class="space-y-4">
      @csrf
      @method('PUT')

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Nama Toko / UMKM *</label>
          <input type="text" name="merchant_name" id="edit_merchant_name" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Stand / Booth *</label>
          <input type="text" name="booth_number" id="edit_booth_number" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
        </div>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Nama Pemilik *</label>
          <input type="text" name="owner_name" id="edit_owner_name" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">No WhatsApp *</label>
          <input type="text" name="phone" id="edit_phone" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
        </div>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Produk</label>
        <input type="text" name="category" id="edit_category" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
      </div>

      <!-- Bank Details -->
      <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200/80 space-y-3">
        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Rekening Pencairan Dana Klaim:</p>
        <div class="grid grid-cols-3 gap-2">
          <div>
            <label class="block text-[10px] font-bold text-slate-600 mb-1">Bank</label>
            <input type="text" name="bank_name" id="edit_bank_name" class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs">
          </div>
          <div>
            <label class="block text-[10px] font-bold text-slate-600 mb-1">No Rekening</label>
            <input type="text" name="bank_account_number" id="edit_bank_account_number" class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs">
          </div>
          <div>
            <label class="block text-[10px] font-bold text-slate-600 mb-1">Atas Nama</label>
            <input type="text" name="bank_account_holder" id="edit_bank_account_holder" class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs">
          </div>
        </div>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Ganti Password Kasir (Kosongkan jika tidak diganti)</label>
        <input type="password" name="password" placeholder="••••••••" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
      </div>

      <div class="pt-2 flex justify-end space-x-2">
        <button type="button" onclick="closeModal('modalEditMerchant')" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl text-xs">Batal</button>
        <button type="submit" class="px-5 py-2 bg-kmi-600 hover:bg-kmi-700 text-white font-bold rounded-xl text-xs shadow-md shadow-kmi-600/30">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<script>
  function openModal(id) { document.getElementById(id)?.classList.remove('hidden'); }
  function closeModal(id) { document.getElementById(id)?.classList.add('hidden'); }

  function editMerchant(m) {
    const form = document.getElementById('editMerchantForm');
    form.action = `/admin/merchants/${m.intMerchant_ID}`;
    document.getElementById('edit_merchant_name').value = m.txtMerchantName || '';
    document.getElementById('edit_booth_number').value = m.txtBoothNumber || '';
    document.getElementById('edit_owner_name').value = m.txtOwnerName || '';
    document.getElementById('edit_phone').value = m.txtPhone || '';
    document.getElementById('edit_category').value = m.txtCategory || '';
    document.getElementById('edit_bank_name').value = m.txtBankName || '';
    document.getElementById('edit_bank_account_number').value = m.txtBankAccountNumber || '';
    document.getElementById('edit_bank_account_holder').value = m.txtBankAccountHolder || '';
    openModal('modalEditMerchant');
  }
</script>
@endsection

