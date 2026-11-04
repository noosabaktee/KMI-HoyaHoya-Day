<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Employee;
use App\Models\Merchant;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use App\Services\BarcodeService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $barcodeService = app(BarcodeService::class);

        // Seed Master Data
        $this->call([
            DepartmentSeeder::class,
            CategorySeeder::class,
            UnitSeeder::class,
        ]);

        // 1. Initial Settings
        Setting::setVal('festival_name', 'Hoya-Hoya Day Festival 2026', 'Nama Acara Festival KMI');
        Setting::setVal('company_name', 'PT Kalbe Morinaga Indonesia', 'Nama Perusahaan Penyelenggara');
        Setting::setVal('default_voucher_amount', '50000', 'Nominal Default Voucher Digital (Rp)');
        Setting::setVal('mpwa_server', env('MPWA_SERVER', 'https://whatsapp.intconnect.id'), 'Server Gateway MPWA');
        Setting::setVal('mpwa_sender', env('MPWA_SENDER', '62888xxxx'), 'Nomor Pengirim WhatsApp MPWA');

        // 2. Admin User
        $admin = User::create([
            'txtUsername' => 'admin',
            'txtFullName' => 'Panitia Festival KMI',
            'txtEmail' => 'admin@kmi.kalbe.co.id',
            'txtPassword' => Hash::make('password'),
            'txtRole' => 'admin',
            'intMerchant_ID' => null,
            'txtPhone' => '6287887504946',
            'bitActive' => 1,
            'txtInsertedBy' => 'SYSTEM',
            'txtUpdatedBy' => 'SYSTEM',
        ]);

        // 3. Create UMKM 1: Dapur Nusantara Bu Siti
        $m1 = Merchant::create([
            'txtMerchantCode' => 'UMKM-001',
            'txtMerchantName' => 'Dapur Nusantara Bu Siti',
            'txtOwnerName' => 'Siti Rahayu',
            'txtPhone' => '6281298765401',
            'txtBoothNumber' => 'Booth A-01',
            'txtCategory' => 'Makanan Tradisional',
            'txtDescription' => 'Aneka masakan tradisional nusantara gurih & nikmat.',
            'txtBankName' => 'BCA',
            'txtBankAccountNumber' => '8830123456',
            'txtBankAccountHolder' => 'Siti Rahayu',
            'bitActive' => 1,
            'txtInsertedBy' => 'SYSTEM',
            'txtUpdatedBy' => 'SYSTEM',
        ]);

        $u1 = User::create([
            'txtUsername' => 'umkm_siti',
            'txtFullName' => 'Kasir Bu Siti',
            'txtEmail' => 'siti@umkm.local',
            'txtPassword' => Hash::make('password'),
            'txtRole' => 'umkm',
            'intMerchant_ID' => $m1->intMerchant_ID,
            'txtPhone' => '6281298765401',
            'bitActive' => 1,
            'txtInsertedBy' => 'SYSTEM',
            'txtUpdatedBy' => 'SYSTEM',
        ]);

        $catFood1 = Category::create([
            'intMerchant_ID' => $m1->intMerchant_ID,
            'txtCategoryName' => 'Makanan Utama',
            'txtCategorySlug' => 'makanan-utama',
            'bitActive' => 1,
            'txtInsertedBy' => 'SYSTEM',
            'txtUpdatedBy' => 'SYSTEM',
        ]);

        $catDrink1 = Category::create([
            'intMerchant_ID' => $m1->intMerchant_ID,
            'txtCategoryName' => 'Minuman & Takjil',
            'txtCategorySlug' => 'minuman-takjil',
            'bitActive' => 1,
            'txtInsertedBy' => 'SYSTEM',
            'txtUpdatedBy' => 'SYSTEM',
        ]);

        $p1 = Product::create([
            'intMerchant_ID' => $m1->intMerchant_ID,
            'intCategory_ID' => $catFood1->intCategory_ID,
            'txtProductCode' => 'SITI-01',
            'txtProductName' => 'Nasi Liwet Ayam Suwir Lengkap',
            'floatPrice' => 25000,
            'intStock' => 50,
            'txtUnit' => 'Porsi',
            'txtProductImage' => 'images/menu/rice-bowl.webp',
            'bitActive' => 1,
            'txtInsertedBy' => 'SYSTEM',
            'txtUpdatedBy' => 'SYSTEM',
        ]);

        $p2 = Product::create([
            'intMerchant_ID' => $m1->intMerchant_ID,
            'intCategory_ID' => $catFood1->intCategory_ID,
            'txtProductCode' => 'SITI-02',
            'txtProductName' => 'Nasi Bakar Cumi Cabe Ijo',
            'floatPrice' => 20000,
            'intStock' => 45,
            'txtUnit' => 'Porsi',
            'bitActive' => 1,
            'txtInsertedBy' => 'SYSTEM',
            'txtUpdatedBy' => 'SYSTEM',
        ]);

        $p3 = Product::create([
            'intMerchant_ID' => $m1->intMerchant_ID,
            'intCategory_ID' => $catDrink1->intCategory_ID,
            'txtProductCode' => 'SITI-03',
            'txtProductName' => 'Es Cendol Nangka Santan Gurih',
            'floatPrice' => 12000,
            'intStock' => 60,
            'txtUnit' => 'Cup',
            'bitActive' => 1,
            'txtInsertedBy' => 'SYSTEM',
            'txtUpdatedBy' => 'SYSTEM',
        ]);

        // 4. Create UMKM 2: Kopi Klangenan Morinaga
        $m2 = Merchant::create([
            'txtMerchantCode' => 'UMKM-002',
            'txtMerchantName' => 'Kopi Klangenan Morinaga',
            'txtOwnerName' => 'Ahmad Fauzi',
            'txtPhone' => '6281298765402',
            'txtBoothNumber' => 'Booth B-03',
            'txtCategory' => 'Kopi & Minuman',
            'txtDescription' => 'Kopi espresso artisan dan mocktail segar penambah energi.',
            'txtBankName' => 'Mandiri',
            'txtBankAccountNumber' => '1320098765432',
            'txtBankAccountHolder' => 'Ahmad Fauzi',
            'bitActive' => 1,
            'txtInsertedBy' => 'SYSTEM',
            'txtUpdatedBy' => 'SYSTEM',
        ]);

        $u2 = User::create([
            'txtUsername' => 'umkm_kopi',
            'txtFullName' => 'Barista Ahmad',
            'txtEmail' => 'ahmad@umkm.local',
            'txtPassword' => Hash::make('password'),
            'txtRole' => 'umkm',
            'intMerchant_ID' => $m2->intMerchant_ID,
            'txtPhone' => '6281298765402',
            'bitActive' => 1,
            'txtInsertedBy' => 'SYSTEM',
            'txtUpdatedBy' => 'SYSTEM',
        ]);

        $catCoffee2 = Category::create([
            'intMerchant_ID' => $m2->intMerchant_ID,
            'txtCategoryName' => 'Espresso & Latte',
            'txtCategorySlug' => 'espresso-latte',
            'bitActive' => 1,
            'txtInsertedBy' => 'SYSTEM',
            'txtUpdatedBy' => 'SYSTEM',
        ]);

        $p4 = Product::create([
            'intMerchant_ID' => $m2->intMerchant_ID,
            'intCategory_ID' => $catCoffee2->intCategory_ID,
            'txtProductCode' => 'KOPI-01',
            'txtProductName' => 'Es Kopi Susu Gula Aren Spesial',
            'floatPrice' => 18000,
            'intStock' => 80,
            'txtUnit' => 'Cup',
            'txtProductImage' => 'images/menu/latte.webp',
            'bitActive' => 1,
            'txtInsertedBy' => 'SYSTEM',
            'txtUpdatedBy' => 'SYSTEM',
        ]);

        $p5 = Product::create([
            'intMerchant_ID' => $m2->intMerchant_ID,
            'intCategory_ID' => $catCoffee2->intCategory_ID,
            'txtProductCode' => 'KOPI-02',
            'txtProductName' => 'Iced Americano Single Origin',
            'floatPrice' => 15000,
            'intStock' => 70,
            'txtUnit' => 'Cup',
            'txtProductImage' => 'images/menu/iced-americano.webp',
            'bitActive' => 1,
            'txtInsertedBy' => 'SYSTEM',
            'txtUpdatedBy' => 'SYSTEM',
        ]);

        $p6 = Product::create([
            'intMerchant_ID' => $m2->intMerchant_ID,
            'intCategory_ID' => $catCoffee2->intCategory_ID,
            'txtProductCode' => 'KOPI-03',
            'txtProductName' => 'Croissant Butter Pastry',
            'floatPrice' => 15000,
            'intStock' => 40,
            'txtUnit' => 'Pcs',
            'txtProductImage' => 'images/menu/croissant.webp',
            'bitActive' => 1,
            'txtInsertedBy' => 'SYSTEM',
            'txtUpdatedBy' => 'SYSTEM',
        ]);

        // 5. Seed Employees from data_karyawan.xlsx
        $employeesData = [
            ['id' => '12345', 'name' => 'Rama Nusa Bakti', 'phone' => '6287887504946', 'dept' => 'Engineering', 'status' => 'Aktif'],
            ['id' => '22345', 'name' => 'Eko Satrio', 'phone' => '6281234567891', 'dept' => 'Produksi', 'status' => 'Aktif'],
            ['id' => '32345', 'name' => 'Gideo Siagian', 'phone' => '6281234567892', 'dept' => 'QA / QC', 'status' => 'Terkirim'],
            ['id' => '42345', 'name' => 'Khansa Galih', 'phone' => '6281234567893', 'dept' => 'Warehouse', 'status' => 'Terkirim'],
            ['id' => '52345', 'name' => 'Humaira', 'phone' => '6281234567894', 'dept' => 'HRD', 'status' => 'Aktif'],
        ];

        $createdVouchers = [];

        foreach ($employeesData as $empData) {
            $emp = Employee::create([
                'txtEmployeeNik' => $empData['id'],
                'txtEmployeeName' => $empData['name'],
                'txtEmployeePhone' => $empData['phone'],
                'txtDepartment' => $empData['dept'],
                'bitActive' => 1,
                'txtInsertedBy' => 'SYSTEM',
                'txtUpdatedBy' => 'SYSTEM',
            ]);

            $voucherCode = 'HY26-' . $emp->txtEmployeeNik . '-' . strtoupper(Str::random(4));
            $initialBalance = 50000;
            $currentBalance = 50000;

            // Generate Barcode Card
            $cardPath = $barcodeService->createVoucherCardImage(
                $emp->txtEmployeeNik,
                $emp->txtEmployeeName,
                $voucherCode,
                $currentBalance
            );

            $v = Voucher::create([
                'intEmployee_ID' => $emp->intEmployee_ID,
                'txtVoucherCode' => $voucherCode,
                'floatInitialBalance' => $initialBalance,
                'floatCurrentBalance' => $currentBalance,
                'txtStatus' => $empData['status'],
                'txtBarcodeCardPath' => $cardPath,
                'dtmSentAt' => now()->subHours(rand(1, 10)),
                'dtmActivatedAt' => $empData['status'] === 'Aktif' ? now()->subHours(rand(1, 5)) : null,
                'bitActive' => 1,
                'txtInsertedBy' => 'SYSTEM',
                'txtUpdatedBy' => 'SYSTEM',
            ]);

            $createdVouchers[$empData['id']] = [
                'emp' => $emp,
                'voucher' => $v,
            ];
        }

        // 6. Simulate a realistic transaction for Rama Nusa Bakti (using voucher)
        $ramaVoucher = $createdVouchers['12345']['voucher'];
        $ramaEmp = $createdVouchers['12345']['emp'];

        $inv1 = 'INV-' . date('Ymd') . '-01-0001';
        $sale1 = Penjualan::create([
            'txtInvoiceNumber' => $inv1,
            'intMerchant_ID' => $m1->intMerchant_ID,
            'intUser_ID' => $u1->intUser_ID,
            'intEmployee_ID' => $ramaEmp->intEmployee_ID,
            'intVoucher_ID' => $ramaVoucher->intVoucher_ID,
            'floatTotalAmount' => 25000,
            'floatVoucherAmount' => 25000,
            'floatCashAmount' => 0,
            'floatChangeAmount' => 0,
            'txtPaymentType' => 'VOUCHER',
            'txtNotes' => 'Pembelian Nasi Liwet',
            'bitIsClaimed' => 0,
            'txtInsertedBy' => 'umkm_siti',
        ]);

        PenjualanDetail::create([
            'intPenjualan_ID' => $sale1->intPenjualan_ID,
            'intProduct_ID' => $p1->intProduct_ID,
            'txtProductName' => $p1->txtProductName,
            'floatPrice' => 25000,
            'intQuantity' => 1,
            'floatSubtotal' => 25000,
            'txtInsertedBy' => 'umkm_siti',
        ]);

        VoucherUsage::create([
            'intVoucher_ID' => $ramaVoucher->intVoucher_ID,
            'intPenjualan_ID' => $sale1->intPenjualan_ID,
            'floatAmountUsed' => 25000,
            'floatBalanceBefore' => 50000,
            'floatBalanceAfter' => 25000,
            'txtInsertedBy' => 'umkm_siti',
        ]);

        // Update Rama voucher balance
        $ramaVoucher->update([
            'floatCurrentBalance' => 25000,
        ]);

        // Re-generate card with updated balance
        $newCardPath = $barcodeService->createVoucherCardImage(
            $ramaEmp->txtEmployeeNik,
            $ramaEmp->txtEmployeeName,
            $ramaVoucher->txtVoucherCode,
            25000
        );
        $ramaVoucher->update(['txtBarcodeCardPath' => $newCardPath]);

        // 7. Simulate pure cash purchase at Kopi Klangenan
        $inv2 = 'INV-' . date('Ymd') . '-02-0001';
        $sale2 = Penjualan::create([
            'txtInvoiceNumber' => $inv2,
            'intMerchant_ID' => $m2->intMerchant_ID,
            'intUser_ID' => $u2->intUser_ID,
            'intEmployee_ID' => null,
            'intVoucher_ID' => null,
            'floatTotalAmount' => 33000,
            'floatVoucherAmount' => 0,
            'floatCashAmount' => 33000,
            'floatChangeAmount' => 7000,
            'txtPaymentType' => 'CASH',
            'txtNotes' => 'Pembeli umum cash',
            'bitIsClaimed' => 0,
            'txtInsertedBy' => 'umkm_kopi',
        ]);

        PenjualanDetail::create([
            'intPenjualan_ID' => $sale2->intPenjualan_ID,
            'intProduct_ID' => $p4->intProduct_ID,
            'txtProductName' => $p4->txtProductName,
            'floatPrice' => 18000,
            'intQuantity' => 1,
            'floatSubtotal' => 18000,
            'txtInsertedBy' => 'umkm_kopi',
        ]);

        PenjualanDetail::create([
            'intPenjualan_ID' => $sale2->intPenjualan_ID,
            'intProduct_ID' => $p6->intProduct_ID,
            'txtProductName' => $p6->txtProductName,
            'floatPrice' => 15000,
            'intQuantity' => 1,
            'floatSubtotal' => 15000,
            'txtInsertedBy' => 'umkm_kopi',
        ]);
    }
}
