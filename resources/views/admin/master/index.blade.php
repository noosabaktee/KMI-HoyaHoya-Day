@extends('layouts.app')

@section('header_title', 'Master Data Referensi - KMI')

@section('content')
<div class="space-y-6">

  <!-- Header Banner -->
  <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
      <div class="flex items-center space-x-2">
        <span class="px-2.5 py-1 bg-kmi-100 text-kmi-800 rounded-lg text-[10px] font-extrabold uppercase tracking-wider">
          Standardisasi KMI
        </span>
        <span class="text-xs text-slate-400">•</span>
        <span class="text-xs text-slate-500 font-medium">Hoya-Hoya Day Festival 2026</span>
      </div>
      <h2 class="text-xl font-extrabold text-slate-900 mt-1">Master Data Referensi</h2>
      <p class="text-xs text-slate-500 mt-0.5">Kelola data terpusat untuk departemen karyawan, kategori menu festival, dan satuan produk.</p>
    </div>

    <!-- Quick Stats Summary -->
    <div class="flex items-center gap-3">
      <div class="px-4 py-2 bg-slate-50 rounded-2xl border border-slate-200/80 text-center">
        <div class="text-[10px] font-bold text-slate-400 uppercase">Departemen</div>
        <div class="text-base font-extrabold text-slate-900">{{ $stats['total_dept'] }}</div>
      </div>
      <div class="px-4 py-2 bg-slate-50 rounded-2xl border border-slate-200/80 text-center">
        <div class="text-[10px] font-bold text-slate-400 uppercase">Kategori</div>
        <div class="text-base font-extrabold text-slate-900">{{ $stats['total_cat'] }}</div>
      </div>
      <div class="px-4 py-2 bg-slate-50 rounded-2xl border border-slate-200/80 text-center">
        <div class="text-[10px] font-bold text-slate-400 uppercase">Satuan</div>
        <div class="text-base font-extrabold text-slate-900">{{ $stats['total_unit'] }}</div>
      </div>
    </div>
  </div>

  <!-- Tab Switcher Navigation -->
  <div class="flex items-center space-x-2 border-b border-slate-200 pb-2 overflow-x-auto no-scrollbar">
    <a href="{{ route('admin.master.index', ['tab' => 'department']) }}"
      class="flex items-center space-x-2 px-4 py-2.5 rounded-2xl text-xs font-bold transition-all shrink-0 {{ $tab === 'department' ? 'bg-kmi-600 text-white shadow-md shadow-kmi-600/30' : 'bg-white hover:bg-slate-100 text-slate-600 border border-slate-200' }}">
      <i class='bx bx-buildings text-base'></i>
      <span>Departemen KMI</span>
      <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $tab === 'department' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $stats['total_dept'] }}</span>
    </a>

    <a href="{{ route('admin.master.index', ['tab' => 'category']) }}"
      class="flex items-center space-x-2 px-4 py-2.5 rounded-2xl text-xs font-bold transition-all shrink-0 {{ $tab === 'category' ? 'bg-kmi-600 text-white shadow-md shadow-kmi-600/30' : 'bg-white hover:bg-slate-100 text-slate-600 border border-slate-200' }}">
      <i class='bx bx-category text-base'></i>
      <span>Kategori Produk</span>
      <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $tab === 'category' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $stats['total_cat'] }}</span>
    </a>

    <a href="{{ route('admin.master.index', ['tab' => 'unit']) }}"
      class="flex items-center space-x-2 px-4 py-2.5 rounded-2xl text-xs font-bold transition-all shrink-0 {{ $tab === 'unit' ? 'bg-kmi-600 text-white shadow-md shadow-kmi-600/30' : 'bg-white hover:bg-slate-100 text-slate-600 border border-slate-200' }}">
      <i class='bx bx-ruler text-base'></i>
      <span>Satuan Produk</span>
      <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $tab === 'unit' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $stats['total_unit'] }}</span>
    </a>
  </div>

  <!-- TAB 1: DEPARTEMEN -->
  @if($tab === 'department')
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-5 space-y-4">
      <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
        <form action="{{ route('admin.master.index') }}" method="GET" class="relative flex-1 w-full">
          <input type="hidden" name="tab" value="department">
          <i class='bx bx-search absolute left-3.5 top-2.5 text-slate-400 text-lg'></i>
          <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau kode departemen..."
            class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-kmi-600 focus:bg-white">
        </form>

        <button type="button" onclick="openModal('modalAddDept')"
          class="w-full sm:w-auto px-4 py-2 bg-kmi-600 hover:bg-kmi-700 text-white font-bold rounded-xl text-xs flex items-center justify-center space-x-1.5 shadow-sm shadow-kmi-600/30 shrink-0">
          <i class='bx bx-plus text-base'></i>
          <span>Tambah Departemen</span>
        </button>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-600">
          <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px] border-y border-slate-100">
            <tr>
              <th class="py-3 px-4">Kode</th>
              <th class="py-3 px-4">Nama Departemen</th>
              <th class="py-3 px-4">Status</th>
              <th class="py-3 px-4">Dibuat Oleh</th>
              <th class="py-3 px-4 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-medium">
            @forelse($departments as $dept)
              <tr class="hover:bg-slate-50/80 transition-colors">
                <td class="py-3 px-4 font-mono font-bold text-slate-900">{{ $dept->txtDepartmentCode }}</td>
                <td class="py-3 px-4 font-bold text-slate-800">{{ $dept->txtDepartmentName }}</td>
                <td class="py-3 px-4">
                  @if($dept->bitActive)
                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-full font-bold text-[10px]">Aktif</span>
                  @else
                    <span class="px-2 py-0.5 bg-slate-100 text-slate-500 rounded-full font-bold text-[10px]">Non-Aktif</span>
                  @endif
                </td>
                <td class="py-3 px-4 text-slate-400 text-[11px]">{{ $dept->txtInsertedBy ?? 'SYSTEM' }}</td>
                <td class="py-3 px-4 text-center">
                  <div class="flex items-center justify-center space-x-1">
                    <button type="button" onclick="editDept({{ json_encode($dept) }})"
                      class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs" title="Edit Departemen">
                      <i class='bx bx-edit text-base'></i>
                    </button>
                    <form action="{{ route('admin.master.departments.destroy', $dept->intDepartment_ID) }}" method="POST" onsubmit="return confirm('Hapus departemen {{ $dept->txtDepartmentName }}?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs" title="Hapus Departemen">
                        <i class='bx bx-trash text-base'></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="py-8 text-center text-slate-400">Belum ada data departemen.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="pt-2">
        {{ $departments->links() }}
      </div>
    </div>
  @endif

  <!-- TAB 2: KATEGORI PRODUK -->
  @if($tab === 'category')
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-5 space-y-4">
      <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
        <form action="{{ route('admin.master.index') }}" method="GET" class="relative flex-1 w-full">
          <input type="hidden" name="tab" value="category">
          <i class='bx bx-search absolute left-3.5 top-2.5 text-slate-400 text-lg'></i>
          <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama kategori produk..."
            class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-kmi-600 focus:bg-white">
        </form>

        <button type="button" onclick="openModal('modalAddCat')"
          class="w-full sm:w-auto px-4 py-2 bg-kmi-600 hover:bg-kmi-700 text-white font-bold rounded-xl text-xs flex items-center justify-center space-x-1.5 shadow-sm shadow-kmi-600/30 shrink-0">
          <i class='bx bx-plus text-base'></i>
          <span>Tambah Kategori</span>
        </button>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-600">
          <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px] border-y border-slate-100">
            <tr>
              <th class="py-3 px-4">Nama Kategori</th>
              <th class="py-3 px-4">Slug URL</th>
              <th class="py-3 px-4">Produk Terkait</th>
              <th class="py-3 px-4">Status</th>
              <th class="py-3 px-4 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-medium">
            @forelse($categories as $cat)
              <tr class="hover:bg-slate-50/80 transition-colors">
                <td class="py-3 px-4 font-bold text-slate-900">{{ $cat->txtCategoryName }}</td>
                <td class="py-3 px-4 font-mono text-slate-400 text-[11px]">{{ $cat->txtCategorySlug }}</td>
                <td class="py-3 px-4">
                  <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded-full font-bold text-[10px]">
                    {{ $cat->products_count }} Menu
                  </span>
                </td>
                <td class="py-3 px-4">
                  @if($cat->bitActive)
                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-full font-bold text-[10px]">Aktif</span>
                  @else
                    <span class="px-2 py-0.5 bg-slate-100 text-slate-500 rounded-full font-bold text-[10px]">Non-Aktif</span>
                  @endif
                </td>
                <td class="py-3 px-4 text-center">
                  <div class="flex items-center justify-center space-x-1">
                    <button type="button" onclick="editCat({{ json_encode($cat) }})"
                      class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs" title="Edit Kategori">
                      <i class='bx bx-edit text-base'></i>
                    </button>
                    <form action="{{ route('admin.master.categories.destroy', $cat->intCategory_ID) }}" method="POST" onsubmit="return confirm('Hapus kategori {{ $cat->txtCategoryName }}?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs" title="Hapus Kategori">
                        <i class='bx bx-trash text-base'></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="py-8 text-center text-slate-400">Belum ada data kategori master.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="pt-2">
        {{ $categories->links() }}
      </div>
    </div>
  @endif

  <!-- TAB 3: SATUAN / UNIT -->
  @if($tab === 'unit')
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-5 space-y-4">
      <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
        <form action="{{ route('admin.master.index') }}" method="GET" class="relative flex-1 w-full">
          <input type="hidden" name="tab" value="unit">
          <i class='bx bx-search absolute left-3.5 top-2.5 text-slate-400 text-lg'></i>
          <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau kode satuan..."
            class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-kmi-600 focus:bg-white">
        </form>

        <button type="button" onclick="openModal('modalAddUnit')"
          class="w-full sm:w-auto px-4 py-2 bg-kmi-600 hover:bg-kmi-700 text-white font-bold rounded-xl text-xs flex items-center justify-center space-x-1.5 shadow-sm shadow-kmi-600/30 shrink-0">
          <i class='bx bx-plus text-base'></i>
          <span>Tambah Satuan</span>
        </button>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-600">
          <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px] border-y border-slate-100">
            <tr>
              <th class="py-3 px-4">Kode Satuan</th>
              <th class="py-3 px-4">Nama Satuan</th>
              <th class="py-3 px-4">Status</th>
              <th class="py-3 px-4">Dibuat Oleh</th>
              <th class="py-3 px-4 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-medium">
            @forelse($units as $u)
              <tr class="hover:bg-slate-50/80 transition-colors">
                <td class="py-3 px-4 font-mono font-bold text-slate-900">{{ $u->txtUnitCode }}</td>
                <td class="py-3 px-4 font-bold text-slate-800">{{ $u->txtUnitName }}</td>
                <td class="py-3 px-4">
                  @if($u->bitActive)
                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-full font-bold text-[10px]">Aktif</span>
                  @else
                    <span class="px-2 py-0.5 bg-slate-100 text-slate-500 rounded-full font-bold text-[10px]">Non-Aktif</span>
                  @endif
                </td>
                <td class="py-3 px-4 text-slate-400 text-[11px]">{{ $u->txtInsertedBy ?? 'SYSTEM' }}</td>
                <td class="py-3 px-4 text-center">
                  <div class="flex items-center justify-center space-x-1">
                    <button type="button" onclick="editUnit({{ json_encode($u) }})"
                      class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs" title="Edit Satuan">
                      <i class='bx bx-edit text-base'></i>
                    </button>
                    <form action="{{ route('admin.master.units.destroy', $u->intUnit_ID) }}" method="POST" onsubmit="return confirm('Hapus satuan {{ $u->txtUnitName }}?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs" title="Hapus Satuan">
                        <i class='bx bx-trash text-base'></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="py-8 text-center text-slate-400">Belum ada data satuan.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="pt-2">
        {{ $units->links() }}
      </div>
    </div>
  @endif

</div>

<!-- ================= MODALS ================= -->

<!-- Modal Add Department -->
<div id="modalAddDept" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl relative">
    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
      <h3 class="text-base font-extrabold text-slate-900">Tambah Departemen KMI</h3>
      <button type="button" onclick="closeModal('modalAddDept')" class="p-1 text-slate-400 hover:text-slate-700">
        <i class='bx bx-x text-2xl'></i>
      </button>
    </div>

    <form action="{{ route('admin.master.departments.store') }}" method="POST" class="space-y-4">
      @csrf
      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Kode Departemen *</label>
        <input type="text" name="department_code" required placeholder="Contoh: MFG, DNP, BDA, HC" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs uppercase font-mono">
      </div>
      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Departemen *</label>
        <input type="text" name="department_name" required placeholder="Contoh: Manufacturing" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">
      </div>
      <div class="flex items-center space-x-2 pt-1">
        <input type="checkbox" name="active" id="dept_add_active" value="1" checked class="rounded text-kmi-600 focus:ring-kmi-500">
        <label for="dept_add_active" class="text-xs text-slate-700 font-semibold cursor-pointer">Status Aktif</label>
      </div>

      <div class="pt-2 flex justify-end space-x-2">
        <button type="button" onclick="closeModal('modalAddDept')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs">Batal</button>
        <button type="submit" class="px-5 py-2 bg-kmi-600 hover:bg-kmi-700 text-white font-bold rounded-xl text-xs shadow-md shadow-kmi-600/30">Simpan Departemen</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Department -->
<div id="modalEditDept" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl relative">
    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
      <h3 class="text-base font-extrabold text-slate-900">Edit Departemen</h3>
      <button type="button" onclick="closeModal('modalEditDept')" class="p-1 text-slate-400 hover:text-slate-700">
        <i class='bx bx-x text-2xl'></i>
      </button>
    </div>

    <form id="formEditDept" action="" method="POST" class="space-y-4">
      @csrf
      @method('PUT')
      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Kode Departemen *</label>
        <input type="text" name="department_code" id="edit_dept_code" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs uppercase font-mono">
      </div>
      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Departemen *</label>
        <input type="text" name="department_name" id="edit_dept_name" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">
      </div>
      <div class="flex items-center space-x-2 pt-1">
        <input type="checkbox" name="active" id="edit_dept_active" value="1" class="rounded text-kmi-600 focus:ring-kmi-500">
        <label for="edit_dept_active" class="text-xs text-slate-700 font-semibold cursor-pointer">Status Aktif</label>
      </div>

      <div class="pt-2 flex justify-end space-x-2">
        <button type="button" onclick="closeModal('modalEditDept')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs">Batal</button>
        <button type="submit" class="px-5 py-2 bg-kmi-600 hover:bg-kmi-700 text-white font-bold rounded-xl text-xs shadow-md shadow-kmi-600/30">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Add Category -->
<div id="modalAddCat" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl relative">
    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
      <h3 class="text-base font-extrabold text-slate-900">Tambah Kategori Produk Master</h3>
      <button type="button" onclick="closeModal('modalAddCat')" class="p-1 text-slate-400 hover:text-slate-700">
        <i class='bx bx-x text-2xl'></i>
      </button>
    </div>

    <form action="{{ route('admin.master.categories.store') }}" method="POST" class="space-y-4">
      @csrf
      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Kategori *</label>
        <input type="text" name="category_name" required placeholder="Contoh: Makanan Utama, Minuman Dingin" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">
      </div>
      <div class="flex items-center space-x-2 pt-1">
        <input type="checkbox" name="active" id="cat_add_active" value="1" checked class="rounded text-kmi-600 focus:ring-kmi-500">
        <label for="cat_add_active" class="text-xs text-slate-700 font-semibold cursor-pointer">Status Aktif</label>
      </div>

      <div class="pt-2 flex justify-end space-x-2">
        <button type="button" onclick="closeModal('modalAddCat')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs">Batal</button>
        <button type="submit" class="px-5 py-2 bg-kmi-600 hover:bg-kmi-700 text-white font-bold rounded-xl text-xs shadow-md shadow-kmi-600/30">Simpan Kategori</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Category -->
<div id="modalEditCat" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl relative">
    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
      <h3 class="text-base font-extrabold text-slate-900">Edit Kategori Produk</h3>
      <button type="button" onclick="closeModal('modalEditCat')" class="p-1 text-slate-400 hover:text-slate-700">
        <i class='bx bx-x text-2xl'></i>
      </button>
    </div>

    <form id="formEditCat" action="" method="POST" class="space-y-4">
      @csrf
      @method('PUT')
      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Kategori *</label>
        <input type="text" name="category_name" id="edit_cat_name" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">
      </div>
      <div class="flex items-center space-x-2 pt-1">
        <input type="checkbox" name="active" id="edit_cat_active" value="1" class="rounded text-kmi-600 focus:ring-kmi-500">
        <label for="edit_cat_active" class="text-xs text-slate-700 font-semibold cursor-pointer">Status Aktif</label>
      </div>

      <div class="pt-2 flex justify-end space-x-2">
        <button type="button" onclick="closeModal('modalEditCat')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs">Batal</button>
        <button type="submit" class="px-5 py-2 bg-kmi-600 hover:bg-kmi-700 text-white font-bold rounded-xl text-xs shadow-md shadow-kmi-600/30">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Add Unit -->
<div id="modalAddUnit" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl relative">
    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
      <h3 class="text-base font-extrabold text-slate-900">Tambah Satuan Unit</h3>
      <button type="button" onclick="closeModal('modalAddUnit')" class="p-1 text-slate-400 hover:text-slate-700">
        <i class='bx bx-x text-2xl'></i>
      </button>
    </div>

    <form action="{{ route('admin.master.units.store') }}" method="POST" class="space-y-4">
      @csrf
      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Kode Satuan *</label>
        <input type="text" name="unit_code" required placeholder="Contoh: PCS, PORSI, CUP, BTL" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs uppercase font-mono">
      </div>
      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Satuan *</label>
        <input type="text" name="unit_name" required placeholder="Contoh: Pcs, Porsi, Cup" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">
      </div>
      <div class="flex items-center space-x-2 pt-1">
        <input type="checkbox" name="active" id="unit_add_active" value="1" checked class="rounded text-kmi-600 focus:ring-kmi-500">
        <label for="unit_add_active" class="text-xs text-slate-700 font-semibold cursor-pointer">Status Aktif</label>
      </div>

      <div class="pt-2 flex justify-end space-x-2">
        <button type="button" onclick="closeModal('modalAddUnit')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs">Batal</button>
        <button type="submit" class="px-5 py-2 bg-kmi-600 hover:bg-kmi-700 text-white font-bold rounded-xl text-xs shadow-md shadow-kmi-600/30">Simpan Satuan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Unit -->
<div id="modalEditUnit" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl relative">
    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
      <h3 class="text-base font-extrabold text-slate-900">Edit Satuan Unit</h3>
      <button type="button" onclick="closeModal('modalEditUnit')" class="p-1 text-slate-400 hover:text-slate-700">
        <i class='bx bx-x text-2xl'></i>
      </button>
    </div>

    <form id="formEditUnit" action="" method="POST" class="space-y-4">
      @csrf
      @method('PUT')
      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Kode Satuan *</label>
        <input type="text" name="unit_code" id="edit_unit_code" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs uppercase font-mono">
      </div>
      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Satuan *</label>
        <input type="text" name="unit_name" id="edit_unit_name" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">
      </div>
      <div class="flex items-center space-x-2 pt-1">
        <input type="checkbox" name="active" id="edit_unit_active" value="1" class="rounded text-kmi-600 focus:ring-kmi-500">
        <label for="edit_unit_active" class="text-xs text-slate-700 font-semibold cursor-pointer">Status Aktif</label>
      </div>

      <div class="pt-2 flex justify-end space-x-2">
        <button type="button" onclick="closeModal('modalEditUnit')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs">Batal</button>
        <button type="submit" class="px-5 py-2 bg-kmi-600 hover:bg-kmi-700 text-white font-bold rounded-xl text-xs shadow-md shadow-kmi-600/30">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
  function openModal(id) {
    document.getElementById(id)?.classList.remove('hidden');
  }

  function closeModal(id) {
    document.getElementById(id)?.classList.add('hidden');
  }

  function editDept(d) {
    const form = document.getElementById('formEditDept');
    form.action = `/admin/master/departments/${d.intDepartment_ID}`;
    document.getElementById('edit_dept_code').value = d.txtDepartmentCode || '';
    document.getElementById('edit_dept_name').value = d.txtDepartmentName || '';
    document.getElementById('edit_dept_active').checked = !!d.bitActive;
    openModal('modalEditDept');
  }

  function editCat(c) {
    const form = document.getElementById('formEditCat');
    form.action = `/admin/master/categories/${c.intCategory_ID}`;
    document.getElementById('edit_cat_name').value = c.txtCategoryName || '';
    document.getElementById('edit_cat_active').checked = !!c.bitActive;
    openModal('modalEditCat');
  }

  function editUnit(u) {
    const form = document.getElementById('formEditUnit');
    form.action = `/admin/master/units/${u.intUnit_ID}`;
    document.getElementById('edit_unit_code').value = u.txtUnitCode || '';
    document.getElementById('edit_unit_name').value = u.txtUnitName || '';
    document.getElementById('edit_unit_active').checked = !!u.bitActive;
    openModal('modalEditUnit');
  }
</script>
@endpush

