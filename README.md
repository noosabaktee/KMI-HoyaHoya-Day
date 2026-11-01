# KMI Hoya-Hoya Day Festival 2026 — Digital POS & Employee Voucher System
**PT Kalbe Morinaga Indonesia (KMI)**

Aplikasi Point of Sale (POS) & Manajemen Distribusi Voucher Digital Karyawan berbasis **Laravel + PostgreSQL** untuk mendukung pemberdayaan Usaha Mikro, Kecil, dan Menengah (UMKM) lokal binaan perusahaan pada acara tahunan **"Hoya-Hoya Day Festival 2026"**.

---

## 🌟 Fitur Utama

### 1. Panel Administrator / Panitia Festival
- **Dashboard Monitoring Perputaran Ekonomi Real-time:**
  - Total omzet festival (Voucher + Transaksi Tunai).
  - Peringkat booth/stand UMKM terlaris.
  - Grafik komposisi pembayaran (Voucher vs Cash) dan tren penjualan per jam.
- **Distribusi Barcode & Voucher Karyawan:**
  - Generate voucher perorangan (NIK, Nama Lengkap, No WhatsApp, Nominal, Departemen).
  - Generate massal sekaligus melalui impor file Excel (format kolom: `ID`, `Nama`, `HP` seperti file `data_karyawan.xlsx`).
  - Desain kartu barcode resmi bergambar PT Kalbe Morinaga Indonesia lengkap dengan Barcode 128, QR Code, Nama Karyawan, NIK, dan Nominal Saldo.
  - Unduh template format Excel (`Template_Data_Karyawan_KMI.xlsx`).
- **Integrasi Gateway WhatsApp (MPWA):**
  - Mengirim kartu barcode dan pesan pemberitahuan otomatis ke WhatsApp karyawan.
  - Tracking status: **Dibuat** &rarr; **Terkirim** (menunggu balasan) &rarr; **Aktif** (setelah karyawan membalas "Diterima/OK") &rarr; **Habis** (saldo Rp 0).
  - Webhook URL otomatis penerima balasan WhatsApp untuk mengubah status menjadi **Aktif**.
  - Opsi aktivasi manual oleh admin.
- **Persetujuan & Pencairan Klaim Reimbursement UMKM:**
  - Review pengajuan klaim dana voucher dari UMKM.
  - Verifikasi rincian transaksi penjualan voucher yang ditagihkan.
  - Cetak **Invoice Klaim Resmi PT Kalbe Morinaga Indonesia**.
  - Tandai status klaim: *Diajukan* &rarr; *Disetujui* &rarr; *Dibayar*.
- **Manajemen Booth & Mitra UMKM:**
  - Pendaftaran stand, nomor booth, nama pemilik, kontak, dan nomor rekening bank pencairan dana.
  - Pembuatan akun login kasir untuk masing-masing stand.
- **Laporan & Export Excel:**
  - Rekapitulasi transaksi penjualan dan ekspor ke format `.xlsx`.
- **Halaman Dokumentasi Lengkap:**
  - Panduan interaktif, SOP alur bisnis, arsitektur sistem, dan panduan kasir langsung di dalam dashboard admin (`/admin/docs`).

### 2. Panel Kasir Mitra UMKM (Touchscreen & Mobile-Friendly)
- **Kasir POS & Scanner Barcode Cepat:**
  - Pencarian menu, filter kategori, dan keranjang belanja interaktif.
  - Pemindai Barcode / QR Code langsung melalui kamera perangkat (HP/laptop/tablet) atau barcode scanner USB.
  - Pengecekan validitas dan saldo voucher secara instan.
- **Kalkulasi Pembayaran Fleksibel (Split Bill Pintar):**
  - **Lunas Voucher:** Saldo voucher mencukupi &rarr; saldo terpotong penuh, tunai Rp 0.
  - **Split Payment:** Saldo voucher tidak mencukupi &rarr; seluruh sisa voucher dihabiskan, sisa kekurangan dihitung otomatis untuk dibayarkan secara tunai (cash) oleh karyawan, lengkap dengan kalkulator uang kembalian.
  - **Pembayaran Tunai Penuh:** Melayani pengunjung umum atau transaksi tanpa voucher.
- **Cetak Struk Transaksi:**
  - Format struk thermal standar (58mm/80mm) yang mencantumkan nama karyawan, rincian menu, potongan voucher, tunai, dan sisa saldo voucher.
- **Manajemen Produk & Stok:**
  - Tambah menu baru, pasang foto produk, atur harga, dan perbarui stok langsung di booth saat festival berlangsung.
- **Pengajuan Klaim Saldo ke Perusahaan:**
  - Pemantauan saldo voucher yang belum diklaim.
  - Pengajuan pencairan dana ke PT Kalbe Morinaga Indonesia dengan 1 klik.
  - Cetak Invoice Klaim Resmi lengkap dengan data rekening bank.

---

## 🏛️ Kepatuhan Standar Database KMI (`KMI_Database_Standard.md`)

Seluruh struktur database PostgreSQL dibangun dari nol dan mematuhi konvensi resmi KMI:
1. **Nama Database:** `KMIHOYAENG2026` (`Company + App + Dept + Year`).
2. **Prefix Tabel:**
   - **Master (`m`):** `mUser`, `mMerchant`, `mCategory`, `mProduct`, `mEmployee`, `mVoucher`, `mSetting`.
   - **Transaksi (`tr`):** `trPenjualan`, `trVoucherUsage`, `trClaimReimbursement`.
   - **Detail Transaksi (`trTable_Details`):** `trPenjualan_Details`, `trClaimReimbursement_Details`.
   - **Log (`log`):** `logWhatsApp`, `logActivity`.
3. **Prefix Kolom Tipe Data:**
   - `int`: Integer (`intUser_ID`, `intProduct_ID`, `intQuantity`, dll)
   - `txt`: String/Text (`txtUsername`, `txtProductName`, `txtVoucherCode`, dll)
   - `dtm`: DateTime (`dtmInserted`, `dtmUpdated`, `dtmSubmittedAt`, dll)
   - `bit`: Nilai biner 0 atau 1 (`bitActive`, `bitIsClaimed`)
   - `float`: Angka desimal (`floatPrice`, `floatTotalAmount`, `floatVoucherAmount`, dll)
4. **Kolom Wajib KMI:**
   - Master: `txtInsertedBy`, `dtmInserted`, `txtUpdatedBy`, `dtmUpdated`, `bitActive`.
   - Transaksi & Detail: `txtInsertedBy`, `dtmInserted`.
   - Log: `txtInsertedBy`, `dtmInserted`, `txtUpdatedBy`, `dtmUpdated`.

---

## 🔑 Akun Bawaan (Default Seeder)

| Peran | Username | Password | Keterangan |
|---|---|---|---|
| **Admin / Panitia KMI** | `admin` | `password` | Akses penuh dashboard, barcode, klaim, UMKM, laporan |
| **Mitra UMKM 1** | `umkm_siti` | `password` | Dapur Nusantara Bu Siti (Booth A-01) |
| **Mitra UMKM 2** | `umkm_kopi` | `password` | Kopi Klangenan Morinaga (Booth B-03) |

---

## ⚙️ Konfigurasi Environment (`.env`)

```env
APP_NAME="Hoya-Hoya Day Festival - Kalbe Morinaga Indonesia"
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=KMIHOYAENG2026
DB_USERNAME=postgres
DB_PASSWORD=root

# MPWA WhatsApp Gateway
MPWA_SERVER=https://whatsapp.intconnect.id
MPWA_API_KEY=####
MPWA_SENDER=62888xxxx
MPWA_FOOTER="Sent via KMI Hoya-Hoya Day Festival"

# Festival
DEFAULT_VOUCHER_AMOUNT=50000
```

---

## 🚀 Menjalankan Aplikasi

1. **Jalankan Migrasi & Seeder Database:**
   ```bash
   php artisan migrate:fresh --seed
   ```
2. **Hubungkan Storage:**
   ```bash
   php artisan storage:link
   ```
3. **Jalankan Server Development:**
   ```bash
   php artisan serve
   ```
4. Buka di browser: `http://localhost:8000`

---

## 🧪 Menjalankan Automated Test
```bash
php artisan test
```
*Seluruh unit & feature tests (autentikasi, pembuatan barcode, webhook WhatsApp, split payment POS, klaim reimbursement) teruji 100% PASS.*
