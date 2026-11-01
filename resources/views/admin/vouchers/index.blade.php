@extends('layouts.app')

@section('header_title', 'Manajemen Voucher & Barcode Karyawan - Hoya-Hoya Festival')

@section('content')
<div class="space-y-6">

  <!-- Header Banner & Action Buttons -->
  <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
      <div class="flex items-center space-x-2">
        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
        <h1 class="text-xl font-extrabold text-slate-900">Distribusi Barcode & Voucher Digital</h1>
      </div>
      <p class="text-xs text-slate-500 mt-1">
        Buat barcode spesial bertema Kalbe Morinaga Indonesia, kirim otomatis via WhatsApp, dan pantau konfirmasi penerimaan.
      </p>
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center flex-wrap gap-2">
      <a href="{{ route('admin.vouchers.template') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-colors flex items-center space-x-1.5">
        <i class='bx bx-download text-base'></i>
        <span>Unduh Template Excel</span>
      </a>

      <button type="button" onclick="openModal('modalBulk')" class="px-4 py-2.5 bg-emerald-50 hover:bg-emerald-100 text-kmi-700 border border-emerald-200 rounded-xl text-xs font-bold transition-colors flex items-center space-x-2">
        <i class='bx bx-file text-base text-kmi-600'></i>
        <span>Generate Sekaligus (Excel)</span>
      </button>

      <button type="button" onclick="openModal('modalSingle')" class="px-4 py-2.5 bg-kmi-600 hover:bg-kmi-700 text-white rounded-xl text-xs font-bold shadow-md shadow-kmi-600/30 transition-all flex items-center space-x-2">
        <i class='bx bx-plus-circle text-base'></i>
        <span>+ Generate 1 Karyawan</span>
      </button>
    </div>
  </div>

  <!-- Status Cards Grid -->
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
      <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Barcode Dibuat</div>
      <div class="text-xl font-extrabold text-slate-900 mt-1">{{ $stats['total'] }} Karyawan</div>
      <div class="text-[10px] text-slate-500 mt-0.5">Alokasi: Rp {{ number_format($stats['total_saldo_awal'], 0, ',', '.') }}</div>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-amber-100 bg-amber-50/20 shadow-sm">
      <div class="text-[11px] font-bold uppercase tracking-wider text-amber-600">Status Terkirim (Menunggu Balasan)</div>
      <div class="text-xl font-extrabold text-amber-700 mt-1">{{ $stats['terkirim'] }}</div>
      <div class="text-[10px] text-amber-600/80 mt-0.5">Karyawan belum membalas WA</div>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-emerald-100 bg-emerald-50/20 shadow-sm">
      <div class="text-[11px] font-bold uppercase tracking-wider text-kmi-700">Status Aktif (Terkonfirmasi)</div>
      <div class="text-xl font-extrabold text-kmi-800 mt-1">{{ $stats['aktif'] }}</div>
      <div class="text-[10px] text-kmi-600/80 mt-0.5">Siap digunakan belanja di booth</div>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
      <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Status Habis (Rp 0)</div>
      <div class="text-xl font-extrabold text-slate-600 mt-1">{{ $stats['habis'] }}</div>
      <div class="text-[10px] text-slate-500 mt-0.5">Sisa Saldo: Rp {{ number_format($stats['total_sisa_saldo'], 0, ',', '.') }}</div>
    </div>
  </div>

  <!-- Filter & Search Bar -->
  <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
    <form action="{{ route('admin.vouchers.index') }}" method="GET" class="flex-1 flex flex-col sm:flex-row items-center gap-2 w-full">
      <div class="relative flex-1 w-full">
        <i class='bx bx-search absolute left-3 top-2.5 text-slate-400 text-lg'></i>
        <input type="text" name="search" value="{{ $search }}" placeholder="Cari NIK, Nama Karyawan, No HP, atau Kode Voucher..."
          class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-kmi-600 focus:bg-white">
      </div>

      <select name="status" class="py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-kmi-600 w-full sm:w-auto">
        <option value="">Semua Status</option>
        <option value="Dibuat" {{ $status === 'Dibuat' ? 'selected' : '' }}>Dibuat</option>
        <option value="Terkirim" {{ $status === 'Terkirim' ? 'selected' : '' }}>Terkirim</option>
        <option value="Aktif" {{ $status === 'Aktif' ? 'selected' : '' }}>Aktif</option>
        <option value="Habis" {{ $status === 'Habis' ? 'selected' : '' }}>Habis</option>
      </select>

      <button type="submit" class="px-4 py-2 bg-slate-800 text-white font-bold rounded-xl text-xs hover:bg-slate-900 w-full sm:w-auto">
        Filter
      </button>
      @if($search || $status)
        <a href="{{ route('admin.vouchers.index') }}" class="p-2 text-slate-400 hover:text-rose-600 text-xs">Reset</a>
      @endif
    </form>
  </div>

  <!-- Employee Voucher Table -->
  <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-slate-50/80 text-slate-400 border-b border-slate-100 uppercase tracking-wider text-[10px]">
          <tr>
            <th class="py-3.5 px-4">NIK (ID)</th>
            <th class="py-3.5 px-4">Nama Karyawan</th>
            <th class="py-3.5 px-4">No HP WhatsApp</th>
            <th class="py-3.5 px-4">Kode Voucher</th>
            <th class="py-3.5 px-4">Saldo (Sisa / Awal)</th>
            <th class="py-3.5 px-4">Status</th>
            <th class="py-3.5 px-4 text-center">Aksi & Kartu</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($employees as $emp)
            @php $v = $emp->voucher; @endphp
            <tr class="hover:bg-slate-50/70">
              <td class="py-3.5 px-4 font-mono font-bold text-slate-900">{{ $emp->txtEmployeeNik }}</td>
              <td class="py-3.5 px-4">
                <div class="font-bold text-slate-900">{{ $emp->txtEmployeeName }}</div>
                <div class="text-[10px] text-slate-400">{{ $emp->txtDepartment ?? 'Karyawan KMI' }}</div>
              </td>
              <td class="py-3.5 px-4 font-mono text-slate-700">
                <a href="https://wa.me/{{ $emp->txtEmployeePhone }}" target="_blank" class="hover:text-kmi-600 flex items-center space-x-1">
                  <i class='bx bxl-whatsapp text-emerald-600 text-sm'></i>
                  <span>{{ $emp->txtEmployeePhone }}</span>
                </a>
              </td>
              <td class="py-3.5 px-4 font-mono font-semibold text-slate-800">
                {{ $v ? $v->txtVoucherCode : '-' }}
              </td>
              <td class="py-3.5 px-4">
                @if($v)
                  <span class="font-extrabold text-emerald-800">Rp {{ number_format($v->floatCurrentBalance, 0, ',', '.') }}</span>
                  <div class="text-[10px] text-slate-400">Awal: Rp {{ number_format($v->floatInitialBalance, 0, ',', '.') }}</div>
                @else
                  -
                @endif
              </td>
              <td class="py-3.5 px-4">
                @if(!$v)
                  <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-full font-semibold text-[10px]">Belum Ada</span>
                @elseif($v->txtStatus === 'Aktif')
                  <span class="px-2.5 py-1 bg-emerald-100 text-kmi-800 rounded-full font-bold text-[10px] inline-flex items-center space-x-1">
                    <i class='bx bx-check-circle'></i>
                    <span>Aktif</span>
                  </span>
                @elseif($v->txtStatus === 'Terkirim')
                  <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full font-bold text-[10px] inline-flex items-center space-x-1" title="Menunggu balasan pesan WhatsApp karyawan">
                    <i class='bx bx-time-five'></i>
                    <span>Terkirim</span>
                  </span>
                @elseif($v->txtStatus === 'Habis')
                  <span class="px-2.5 py-1 bg-slate-100 text-slate-500 rounded-full font-bold text-[10px]">Habis</span>
                @else
                  <span class="px-2.5 py-1 bg-sky-100 text-sky-800 rounded-full font-bold text-[10px]">{{ $v->txtStatus }}</span>
                @endif
              </td>
              <td class="py-3.5 px-4">
                <div class="flex items-center justify-center space-x-1.5">
                  @if($v && $v->txtBarcodeCardPath)
                    <!-- Preview Card Button -->
                    <button type="button" onclick="previewCard('{{ asset('storage/' . $v->txtBarcodeCardPath) }}', '{{ $emp->txtEmployeeName }}', '{{ $v->txtVoucherCode }}')"
                      class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs" title="Lihat Kartu Barcode">
                      <i class='bx bx-id-card text-base'></i>
                    </button>
                  @endif

                  <!-- Resend WhatsApp Button -->
                  <form action="{{ route('admin.vouchers.resend-wa', $emp->intEmployee_ID) }}" method="POST" onsubmit="return confirm('Kirim ulang barcode voucher ke nomor WhatsApp karyawan ini?')">
                    @csrf
                    <button type="submit" class="p-1.5 bg-emerald-50 hover:bg-emerald-100 text-kmi-700 rounded-lg text-xs" title="Kirim Ulang Pesan WhatsApp">
                      <i class='bx bxl-whatsapp text-base'></i>
                    </button>
                  </form>

                  @if($v && $v->txtStatus === 'Terkirim')
                    <!-- Manual Activate Button -->
                    <form action="{{ route('admin.vouchers.activate', $emp->intEmployee_ID) }}" method="POST" onsubmit="return confirm('Aktifkan voucher ini secara manual?')">
                      @csrf
                      <button type="submit" class="px-2 py-1 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-[10px] font-bold" title="Ubah status ke Aktif jika sudah konfirmasi manual">
                        Aktifkan
                      </button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="py-8 text-center text-slate-400">Tidak ada data karyawan ditemukan. Silakan tambahkan atau impor melalui Excel.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($employees->hasPages())
      <div class="p-4 border-t border-slate-100">
        {{ $employees->links() }}
      </div>
    @endif
  </div>

</div>

<!-- Modal 1: Generate Single Karyawan -->
<div id="modalSingle" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100 relative">
    <button type="button" onclick="closeModal('modalSingle')" class="absolute top-5 right-5 text-slate-400 hover:text-slate-700">
      <i class='bx bx-x text-2xl'></i>
    </button>

    <div class="flex items-center space-x-3 mb-6">
      <div class="w-10 h-10 rounded-2xl bg-kmi-100 text-kmi-700 flex items-center justify-center text-xl font-bold">
        <i class='bx bx-user-plus'></i>
      </div>
      <div>
        <h3 class="text-base font-extrabold text-slate-900">Generate 1 Barcode Voucher</h3>
        <p class="text-xs text-slate-500">Buat barcode spesial dan kirim otomatis ke nomor WhatsApp karyawan</p>
      </div>
    </div>

    <form action="{{ route('admin.vouchers.single') }}" method="POST" class="space-y-4">
      @csrf

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Karyawan (ID / NIK) *</label>
        <input type="text" name="nik" required placeholder="Contoh: 12345" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-kmi-600 focus:bg-white">
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap Karyawan *</label>
        <input type="text" name="name" required placeholder="Contoh: Rama Nusa Bakti" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-kmi-600 focus:bg-white">
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Nomor WhatsApp *</label>
        <input type="text" name="phone" required placeholder="Contoh: 6287887504946 atau 0878..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-kmi-600 focus:bg-white">
        <p class="text-[10px] text-slate-400 mt-1">Barcode akan dikirimkan ke nomor ini melalui gateway MPWA.</p>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Saldo (Rp)</label>
          <div class="relative">
            <span class="absolute left-3 top-2.5 text-xs font-bold text-slate-400">Rp</span>
            <input type="text" inputmode="numeric" name="amount" value="50.000" class="number-separator w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:outline-none focus:border-kmi-600 focus:bg-white">
          </div>
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Departemen / Divisi</label>
          <input type="text" name="department" placeholder="Contoh: Engineering" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-kmi-600 focus:bg-white">
        </div>
      </div>

      <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center space-x-2">
        <input type="checkbox" name="send_whatsapp" id="sendWaCheck" value="1" checked class="rounded text-kmi-600 focus:ring-kmi-500">
        <label for="sendWaCheck" class="text-xs text-slate-700 cursor-pointer">
          Kirim barcode dan instruksi konfirmasi langsung ke WhatsApp
        </label>
      </div>

      <div class="pt-2 flex justify-end space-x-2">
        <button type="button" onclick="closeModal('modalSingle')" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs">
          Batal
        </button>
        <button type="submit" class="px-5 py-2.5 bg-kmi-600 hover:bg-kmi-700 text-white font-bold rounded-xl text-xs shadow-md shadow-kmi-600/30">
          Simpan & Generate Barcode
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Modal 2: Bulk Generate from Excel -->
<div id="modalBulk" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100 relative">
    <button type="button" onclick="closeModal('modalBulk')" class="absolute top-5 right-5 text-slate-400 hover:text-slate-700">
      <i class='bx bx-x text-2xl'></i>
    </button>

    <div class="flex items-center space-x-3 mb-6">
      <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-kmi-700 flex items-center justify-center text-xl font-bold">
        <i class='bx bx-upload'></i>
      </div>
      <div>
        <h3 class="text-base font-extrabold text-slate-900">Generate Sekaligus dari Excel</h3>
        <p class="text-xs text-slate-500">Impor massal data karyawan dan buat barcode festival secara otomatis</p>
      </div>
    </div>

    <form action="{{ route('admin.vouchers.bulk') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
      @csrf

      <div class="p-4 border-2 border-dashed border-emerald-300 bg-emerald-50/40 rounded-2xl text-center">
        <i class='bx bx-cloud-upload text-3xl text-kmi-600 mb-2'></i>
        <label class="block text-xs font-bold text-slate-800 mb-1 cursor-pointer">Pilih File Excel (.xlsx / .xls / .csv)</label>
        <input type="file" name="file_excel" accept=".xlsx,.xls,.csv" required class="text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-kmi-600 file:text-white hover:file:bg-kmi-700 cursor-pointer">
        <p class="text-[10px] text-slate-400 mt-2">Mendukung format kolom: <b>ID</b>, <b>Nama</b>, <b>HP</b> (seperti file data_karyawan.xlsx)</p>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Saldo Tiap Voucher (Rp)</label>
        <div class="relative">
          <span class="absolute left-3 top-2.5 text-xs font-bold text-slate-400">Rp</span>
          <input type="text" inputmode="numeric" name="default_amount" value="50.000" class="number-separator w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:outline-none focus:border-kmi-600 focus:bg-white">
        </div>
      </div>

      <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center space-x-2">
        <input type="checkbox" name="send_whatsapp" id="sendWaBulkCheck" value="1" checked class="rounded text-kmi-600 focus:ring-kmi-500">
        <label for="sendWaBulkCheck" class="text-xs text-slate-700 cursor-pointer">
          Kirim barcode dan pesan konfirmasi otomatis ke semua nomor HP karyawan
        </label>
      </div>

      <div class="pt-2 flex justify-between items-center">
        <a href="{{ route('admin.vouchers.template') }}" class="text-xs text-kmi-700 hover:underline font-semibold flex items-center space-x-1">
          <i class='bx bx-download'></i>
          <span>Download Format Excel</span>
        </a>

        <div class="flex space-x-2">
          <button type="button" onclick="closeModal('modalBulk')" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs">
            Batal
          </button>
          <button type="submit" class="px-5 py-2.5 bg-kmi-600 hover:bg-kmi-700 text-white font-bold rounded-xl text-xs shadow-md shadow-kmi-600/30">
            Proses & Kirim Semua
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Modal 3: Preview Barcode Card -->
<div id="modalPreviewCard" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 relative max-h-[92vh] flex flex-col">
    <button type="button" onclick="closeModal('modalPreviewCard')" class="absolute top-5 right-5 text-slate-400 hover:text-slate-700">
      <i class='bx bx-x text-2xl'></i>
    </button>

    <h3 class="text-base font-extrabold text-slate-900 mb-0.5">Kartu QRIS Voucher Digital KMI</h3>
    <p id="previewSub" class="text-xs text-slate-500 mb-3"></p>

    <div class="bg-slate-50 rounded-2xl p-3 border border-slate-200 flex items-center justify-center overflow-auto max-h-[65vh]">
      <img id="cardImageSrc" src="" alt="Voucher QRIS Card" class="max-w-full h-auto max-h-[60vh] object-contain rounded-xl shadow-md">
    </div>

    <div class="mt-4 flex justify-between items-center pt-2 border-t border-slate-100">
      <span class="text-[11px] text-slate-400">Desain Standee QRIS &bull; PT Kalbe Morinaga Indonesia</span>
      <a id="downloadCardBtn" href="" download="" class="px-4 py-2 bg-kmi-600 hover:bg-kmi-700 text-white font-bold rounded-xl text-xs flex items-center space-x-1.5 shadow-md shadow-kmi-600/20">
        <i class='bx bx-download text-base'></i>
        <span>Unduh Kartu</span>
      </a>
    </div>
  </div>
</div>

<script>
  function openModal(id) {
    document.getElementById(id)?.classList.remove('hidden');
  }

  function closeModal(id) {
    document.getElementById(id)?.classList.add('hidden');
  }

  function previewCard(url, name, code) {
    document.getElementById('cardImageSrc').src = url;
    document.getElementById('previewSub').innerText = `${name} • Kartu QRIS Voucher Digital`;
    const dlBtn = document.getElementById('downloadCardBtn');
    dlBtn.href = url;
    dlBtn.download = `Voucher_KMI_${name.replace(/\s+/g, '_')}.png`;
    openModal('modalPreviewCard');
  }
</script>
@endsection

