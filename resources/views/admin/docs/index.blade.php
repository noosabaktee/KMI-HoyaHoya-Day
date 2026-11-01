@extends('layouts.app')

@section('header_title', 'Buku Panduan & Dokumentasi Sistem - Hoya-Hoya Day Festival')

@section('content')
<div class="space-y-8 max-w-5xl mx-auto pb-12">

  <!-- Header Hero -->
  <div class="bg-gradient-to-br from-kmi-800 via-kmi-700 to-emerald-900 rounded-3xl p-8 text-white shadow-xl shadow-kmi-700/20 relative overflow-hidden">
    <div class="relative z-10">
      <div class="inline-flex items-center space-x-2 px-3 py-1 bg-white/15 backdrop-blur-md rounded-full text-xs font-semibold mb-3 text-emerald-100">
        <i class='bx bx-book-open text-festive-500'></i>
        <span>Dokumentasi Resmi & Standar Operasional Prosedur (SOP)</span>
      </div>
      <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Sistem POS Digital & Barcode Voucher Festival</h1>
      <p class="text-sm text-emerald-100 mt-2 max-w-2xl leading-relaxed">
        Panduan komprehensif bagi Panitia Festival, Admin Sistem, dan Kasir Mitra UMKM PT Kalbe Morinaga Indonesia dalam penyelenggaraan Hoya-Hoya Day Festival 2026.
      </p>
    </div>
    <div class="absolute -right-8 -bottom-8 opacity-10 text-white pointer-events-none">
      <i class='bx bx-help-circle text-[220px]'></i>
    </div>
  </div>

  <!-- Navigation Index Pills -->
  <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm flex flex-wrap gap-2 text-xs font-bold sticky top-4 z-20">
    <a href="#latar-belakang" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">1. Latar Belakang</a>
    <a href="#alur-sistem" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">2. Alur Transaksi Digital</a>
    <a href="#panduan-admin" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">3. Panduan Admin & Barcode</a>
    <a href="#panduan-umkm" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">4. Panduan Kasir POS UMKM</a>
    <a href="#whatsapp-mpwa" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">5. Konfigurasi MPWA</a>
    <a href="#kmi-standard" class="px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-kmi-800 transition-colors border border-emerald-200">6. Standar Database KMI</a>
  </div>

  <!-- Section 1: Latar Belakang & Tujuan -->
  <section id="latar-belakang" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-4">
    <div class="flex items-center space-x-3 text-kmi-700">
      <div class="w-8 h-8 rounded-xl bg-kmi-50 flex items-center justify-center font-bold text-lg">1</div>
      <h2 class="text-lg font-extrabold text-slate-900">Latar Belakang & Konsep Acara</h2>
    </div>

    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
      <b>PT Kalbe Morinaga Indonesia (KMI)</b> menyelenggarakan acara tahunan bertajuk <b>"Hoya-Hoya Day Festival"</b> sebagai bentuk kepedulian sosial dan pemberdayaan ekonomi masyarakat sekitar, khususnya para pelaku Usaha Mikro, Kecil, dan Menengah (UMKM) lokal. Dalam festival ini, para mitra UMKM diundang untuk membuka stan bazar/booth.
    </p>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
      <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-100">
        <h4 class="text-xs font-bold text-emerald-900 mb-1">🎟️ Voucher Digital Karyawan</h4>
        <p class="text-xs text-emerald-800/80 leading-relaxed">
          Setiap karyawan diberikan voucher digital senilai <b>Rp 50.000</b> berupa kartu barcode khusus berlogo KMI yang dikirim ke nomor WhatsApp masing-masing.
        </p>
      </div>

      <div class="p-4 rounded-2xl bg-sky-50/60 border border-sky-100">
        <h4 class="text-xs font-bold text-sky-900 mb-1">💳 Transaksi POS Digital</h4>
        <p class="text-xs text-sky-800/80 leading-relaxed">
          Mitra UMKM menggunakan aplikasi POS berbasis web yang responsif untuk memindai barcode karyawan serta melayani pembayaran tunai umum.
        </p>
      </div>

      <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-100">
        <h4 class="text-xs font-bold text-amber-900 mb-1">📈 Perputaran Ekonomi</h4>
        <p class="text-xs text-amber-800/80 leading-relaxed">
          Sistem mencatat seluruh total omzet (Voucher + Tunai) sehingga manajemen KMI dapat mengukur dampak ekonomi riil yang dihasilkan selama festival.
        </p>
      </div>
    </div>
  </section>

  <!-- Section 2: Alur Transaksi Digital -->
  <section id="alur-sistem" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-4">
    <div class="flex items-center space-x-3 text-kmi-700">
      <div class="w-8 h-8 rounded-xl bg-kmi-50 flex items-center justify-center font-bold text-lg">2</div>
      <h2 class="text-lg font-extrabold text-slate-900">Alur Bisnis & Transaksi Digital</h2>
    </div>

    <div class="space-y-4 pt-2">
      <div class="flex items-start space-x-4 p-4 rounded-2xl bg-slate-50 border border-slate-100">
        <span class="w-7 h-7 rounded-full bg-kmi-600 text-white font-bold text-xs flex items-center justify-center shrink-0">1</span>
        <div>
          <h4 class="text-xs font-bold text-slate-900">Pemberian Barcode Voucher ke Karyawan</h4>
          <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">
            Admin mengimpor data karyawan melalui Excel atau form satuan. Sistem secara otomatis mendesain gambar kartu barcode bertema KMI dan mengirimkannya via WhatsApp API. Status awal di sistem: <span class="font-bold text-amber-700">Terkirim</span>.
          </p>
        </div>
      </div>

      <div class="flex items-start space-x-4 p-4 rounded-2xl bg-slate-50 border border-slate-100">
        <span class="w-7 h-7 rounded-full bg-kmi-600 text-white font-bold text-xs flex items-center justify-center shrink-0">2</span>
        <div>
          <h4 class="text-xs font-bold text-slate-900">Konfirmasi Balasan WhatsApp (Aktivasi Otomatis)</h4>
          <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">
            Karyawan membalas pesan WhatsApp tersebut (misal: <em>"Diterima"</em> atau <em>"OK"</em>). Webhook MPWA menerima balasan tersebut dan secara otomatis mengubah status voucher menjadi <span class="font-bold text-kmi-700">Aktif</span>. Admin juga dapat mengaktifkan secara manual jika diperlukan.
          </p>
        </div>
      </div>

      <div class="flex items-start space-x-4 p-4 rounded-2xl bg-slate-50 border border-slate-100">
        <span class="w-7 h-7 rounded-full bg-kmi-600 text-white font-bold text-xs flex items-center justify-center shrink-0">3</span>
        <div>
          <h4 class="text-xs font-bold text-slate-900">Penjualan di Stand UMKM & Pemindaian Voucher</h4>
          <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">
            Karyawan memilih produk di booth UMKM. Kasir mengklik tombol <b>Scan Barcode</b> (menggunakan kamera HP/laptop atau barcode scanner USB). Sistem menampilkan nama karyawan dan sisa saldo yang tersedia.
          </p>
        </div>
      </div>

      <div class="flex items-start space-x-4 p-4 rounded-2xl bg-emerald-50/50 border border-emerald-200">
        <span class="w-7 h-7 rounded-full bg-emerald-700 text-white font-bold text-xs flex items-center justify-center shrink-0">4</span>
        <div>
          <h4 class="text-xs font-bold text-emerald-950">Fleksibilitas Pembayaran (Kasus Saldo Voucher & Split Tunai)</h4>
          <div class="mt-2 space-y-2 text-xs text-emerald-900/90">
            <p>
              &bull; <b>Kasus A (Lunas Penuh dengan Voucher):</b> Total belanja Rp 25.000, saldo voucher Rp 50.000. Sistem memotong saldo Rp 25.000. Sisa saldo karyawan menjadi Rp 25.000. Uang tunai yang dibayar = Rp 0.
            </p>
            <p>
              &bull; <b>Kasus B (Split Payment / Saldo Kurang):</b> Total belanja Rp 35.000, tetapi sisa saldo voucher karyawan tinggal Rp 20.000. Sistem memotong seluruh sisa voucher (Rp 20.000 sehingga saldo menjadi 0), lalu menghitung sisa tagihan secara otomatis yaitu <b>Rp 15.000</b> yang wajib dibayarkan karyawan secara tunai (cash) ke UMKM. Fitur ini memastikan voucher dapat dihabiskan sampai tuntas.
            </p>
            <p>
              &bull; <b>Kasus C (Pembelian Tunai Murni):</b> Pengunjung atau karyawan yang tidak menggunakan voucher dapat membayar tunai penuh. Penjualan tetap tercatat di sistem agar panitia mengetahui total perputaran ekonomi festival.
            </p>
          </div>
        </div>
      </div>

      <div class="flex items-start space-x-4 p-4 rounded-2xl bg-slate-50 border border-slate-100">
        <span class="w-7 h-7 rounded-full bg-kmi-600 text-white font-bold text-xs flex items-center justify-center shrink-0">5</span>
        <div>
          <h4 class="text-xs font-bold text-slate-900">Pencairan Dana (Claim Reimbursement UMKM ke KMI)</h4>
          <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">
            Pada menu <b>Klaim Saldo</b>, mitra UMKM dapat melihat total transaksi voucher yang telah dikumpulkan. Dengan satu klik, UMKM mengajukan klaim ke perusahaan dan mencetak <b>Invoice Klaim Resmi KMI</b> lengkap dengan nomor rekening bank tujuan. Bagian Finance KMI menyetujui dan mentransfer dana penggantian.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- Section 3: Panduan Admin -->
  <section id="panduan-admin" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-4">
    <div class="flex items-center space-x-3 text-kmi-700">
      <div class="w-8 h-8 rounded-xl bg-kmi-50 flex items-center justify-center font-bold text-lg">3</div>
      <h2 class="text-lg font-extrabold text-slate-900">Panduan Administrator / Panitia KMI</h2>
    </div>

    <div class="space-y-4 text-xs sm:text-sm text-slate-600">
      <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
        <h4 class="text-xs font-bold text-slate-900 mb-2">A. Cara Generate Barcode Satu Persatu:</h4>
        <ol class="list-decimal list-inside space-y-1 text-xs">
          <li>Buka menu <b>Voucher & Barcode</b> di sidebar admin.</li>
          <li>Klik tombol <b>+ Generate 1 Karyawan</b>.</li>
          <li>Isi Nomor Karyawan (NIK), Nama Lengkap, Nomor WhatsApp (awalan 62 atau 08), dan nominal saldo.</li>
          <li>Centang opsi <em>"Kirim langsung ke WhatsApp"</em> lalu klik <b>Simpan & Generate Barcode</b>.</li>
          <li>Kartu barcode bergambar resmi KMI akan otomatis dibuat dan dikirim ke karyawan.</li>
        </ol>
      </div>

      <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
        <h4 class="text-xs font-bold text-slate-900 mb-2">B. Cara Impor Massal Menggunakan Excel:</h4>
        <ol class="list-decimal list-inside space-y-1 text-xs">
          <li>Klik tombol <b>Generate Sekaligus (Excel)</b>.</li>
          <li>Anda dapat mengunggah file Excel seperti <code>data_karyawan.xlsx</code> yang berisi kolom: <b>ID</b>, <b>Nama</b>, <b>HP</b>.</li>
          <li>Tersedia juga tombol <b>Unduh Template Excel</b> untuk format standar.</li>
          <li>Pilih file Excel dari komputer Anda, tentukan nominal saldo per voucher (default: Rp 50.000).</li>
          <li>Klik <b>Proses & Kirim Semua</b>. Sistem akan memproses seluruh baris, membuat barcode satu per satu, dan mengirimkan pesan WhatsApp ke masing-masing karyawan.</li>
        </ol>
      </div>

      <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
        <h4 class="text-xs font-bold text-slate-900 mb-2">C. Verifikasi & Approval Klaim Reimbursement UMKM:</h4>
        <ol class="list-decimal list-inside space-y-1 text-xs">
          <li>Buka menu <b>Klaim Reimbursement</b>.</li>
          <li>Pilih klaim yang berstatus <em>Diajukan</em> lalu klik <b>Review</b>.</li>
          <li>Periksa rincian transaksi voucher yang ditagihkan dan nomor rekening bank UMKM.</li>
          <li>Klik <b>Setujui Pengajuan Klaim</b> agar disahkan oleh panitia.</li>
          <li>Setelah bagian Finance KMI melakukan transfer bank, klik <b>Tandai Lunas / Telah Ditransfer</b>.</li>
        </ol>
      </div>
    </div>
  </section>

  <!-- Section 4: Panduan Kasir UMKM -->
  <section id="panduan-umkm" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-4">
    <div class="flex items-center space-x-3 text-kmi-700">
      <div class="w-8 h-8 rounded-xl bg-kmi-50 flex items-center justify-center font-bold text-lg">4</div>
      <h2 class="text-lg font-extrabold text-slate-900">Panduan Pengoperasian POS Kasir UMKM</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs text-slate-600">
      <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
        <h4 class="text-xs font-bold text-slate-900">Langkah Transaksi Kasir:</h4>
        <ul class="list-disc list-inside space-y-1">
          <li>Masuk dengan username & password stand Anda.</li>
          <li>Pilih produk yang dibeli karyawan (klik item untuk menambah ke keranjang).</li>
          <li>Arahkan kamera ke barcode di HP karyawan atau ketik kode voucher.</li>
          <li>Sistem secara otomatis mengecek validitas voucher dan menampilkan sisa saldo karyawan.</li>
          <li>Jika ada sisa tagihan tunai (split bill), masukkan jumlah uang tunai yang diterima untuk menghitung kembalian.</li>
          <li>Klik <b>Selesaikan Pembayaran</b> & cetak struk penjualan bila diperlukan.</li>
        </ul>
      </div>

      <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
        <h4 class="text-xs font-bold text-slate-900">Manajemen Produk & Stok:</h4>
        <ul class="list-disc list-inside space-y-1">
          <li>Buka menu <b>Produk & Stok</b>.</li>
          <li>Klik <b>+ Tambah Produk</b> untuk mendaftarkan menu baru beserta foto, harga, dan porsi.</li>
          <li>Stok akan otomatis berkurang setiap kali terjadi transaksi di kasir POS.</li>
          <li>Anda dapat memperbarui jumlah stok langsung dari tabel daftar produk sewaktu-waktu saat festival berlangsung.</li>
        </ul>
      </div>
    </div>
  </section>

  <!-- Section 5: Konfigurasi MPWA WhatsApp -->
  <section id="whatsapp-mpwa" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-4">
    <div class="flex items-center space-x-3 text-kmi-700">
      <div class="w-8 h-8 rounded-xl bg-kmi-50 flex items-center justify-center font-bold text-lg">5</div>
      <h2 class="text-lg font-extrabold text-slate-900">Integrasi Gateway WhatsApp (MPWA)</h2>
    </div>

    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
      Sistem ini terhubung secara langsung ke server MPWA untuk mengirim kartu voucher bergambar dan menerima webhook konfirmasi aktivasi dari karyawan.
    </p>

    <div class="bg-slate-900 text-slate-200 p-4 rounded-2xl font-mono text-xs overflow-x-auto space-y-2">
      <p class="text-slate-400"># Konfigurasi di file .env:</p>
      <p><span class="text-emerald-400">MPWA_SERVER</span>=https://whatsapp.intconnect.id</p>
      <p><span class="text-emerald-400">MPWA_API_KEY</span>=#### (Diisi dengan API Key MPWA Anda)</p>
      <p><span class="text-emerald-400">MPWA_SENDER</span>=62888xxxx (Nomor pengirim terdaftar)</p>
      <p><span class="text-emerald-400">MPWA_FOOTER</span>="Sent via KMI Hoya-Hoya Day Festival"</p>
    </div>

    <div class="p-4 rounded-2xl bg-sky-50 border border-sky-100 text-xs text-sky-900 space-y-1">
      <h4 class="font-bold">Webhook Aktivasi Otomatis (Saat Karyawan Membalas):</h4>
      <p>Daftarkan URL webhook berikut pada panel dashboard MPWA Anda:</p>
      <code class="font-mono bg-white px-2 py-1 rounded text-sky-800 border border-sky-200 inline-block mt-1">
        POST {{ url('/api/whatsapp/webhook') }}
      </code>
      <p class="mt-1 text-slate-500">Ketika karyawan membalas pesan, MPWA akan mengirimkan payload HTTP POST ke URL ini dan sistem secara instan mengaktifkan voucher terkait.</p>
    </div>
  </section>

  <!-- Section 6: Standard Database KMI Compliance Matrix -->
  <section id="kmi-standard" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-4">
    <div class="flex items-center space-x-3 text-kmi-700">
      <div class="w-8 h-8 rounded-xl bg-kmi-50 flex items-center justify-center font-bold text-lg">6</div>
      <h2 class="text-lg font-extrabold text-slate-900">Kepatuhan Standar Database KMI (KMI_Database_Standard.md)</h2>
    </div>

    <p class="text-xs text-slate-600 leading-relaxed">
      Seluruh skema database PostgreSQL aplikasi ini 100% mematuhi dokumen resmi <code>KMI_Database_Standard.md</code>:
    </p>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[10px]">
          <tr>
            <th class="py-2.5 px-3">Jenis Tabel</th>
            <th class="py-2.5 px-3">Prefix</th>
            <th class="py-2.5 px-3">Tabel di Aplikasi Ini</th>
            <th class="py-2.5 px-3">Kolom Wajib KMI yang Diimplementasikan</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr>
            <td class="py-2.5 px-3 font-semibold text-slate-900">Master Data</td>
            <td class="py-2.5 px-3 font-mono text-emerald-700">m</td>
            <td class="py-2.5 px-3 font-mono">mUser, mMerchant, mCategory, mProduct, mEmployee, mVoucher, mSetting</td>
            <td class="py-2.5 px-3 font-mono text-slate-600">txtInsertedBy, dtmInserted, txtUpdatedBy, dtmUpdated, bitActive</td>
          </tr>
          <tr>
            <td class="py-2.5 px-3 font-semibold text-slate-900">Transaksi</td>
            <td class="py-2.5 px-3 font-mono text-sky-700">tr</td>
            <td class="py-2.5 px-3 font-mono">trPenjualan, trVoucherUsage, trClaimReimbursement</td>
            <td class="py-2.5 px-3 font-mono text-slate-600">txtInsertedBy, dtmInserted</td>
          </tr>
          <tr>
            <td class="py-2.5 px-3 font-semibold text-slate-900">Detail Transaksi</td>
            <td class="py-2.5 px-3 font-mono text-purple-700">tr(Table)_Details</td>
            <td class="py-2.5 px-3 font-mono">trPenjualan_Details, trClaimReimbursement_Details</td>
            <td class="py-2.5 px-3 font-mono text-slate-600">txtInsertedBy, dtmInserted</td>
          </tr>
          <tr>
            <td class="py-2.5 px-3 font-semibold text-slate-900">Log / Audit</td>
            <td class="py-2.5 px-3 font-mono text-amber-700">log</td>
            <td class="py-2.5 px-3 font-mono">logWhatsApp, logActivity</td>
            <td class="py-2.5 px-3 font-mono text-slate-600">txtInsertedBy, dtmInserted, txtUpdatedBy, dtmUpdated</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="p-3 bg-slate-50 rounded-xl text-xs text-slate-500">
      Format Nama Database yang Digunakan: <code class="font-mono font-bold text-slate-800">KMIHOYAENG2026</code> (Format: <code>Company + App + Dept + Year</code> sesuai standar KMI).
    </div>
  </section>

</div>
@endsection

