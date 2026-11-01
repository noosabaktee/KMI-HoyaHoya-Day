<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Merchant;
use App\Models\Penjualan;
use App\Models\Product;
use App\Models\User;
use App\Models\Voucher;
use App\Services\VoucherService;
use App\Services\WhatsAppService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HoyaHoyaFestivalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_admin_can_login_and_access_dashboard(): void
    {
        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticated();

        $dashResponse = $this->get('/admin/dashboard');
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Perputaran Ekonomi');
    }

    public function test_umkm_can_login_and_access_pos(): void
    {
        $response = $this->post('/login', [
            'username' => 'umkm_siti',
            'password' => 'password',
        ]);

        $response->assertRedirect('/umkm/pos');
        $this->assertAuthenticated();

        $posResponse = $this->get('/umkm/pos');
        $posResponse->assertStatus(200);
        $posResponse->assertSee('Keranjang Belanja');
    }

    public function test_generate_single_voucher_and_activate_via_webhook(): void
    {
        $admin = User::where('txtUsername', 'admin')->first();
        $this->actingAs($admin);

        // 1. Generate voucher
        $voucherService = app(VoucherService::class);
        $result = $voucherService->generateSingle('77701', 'Test Karyawan', '6281234567770', 50000, 'HRD', false);

        $this->assertDatabaseHas('mEmployee', ['txtEmployeeNik' => '77701']);
        $this->assertDatabaseHas('mVoucher', ['intEmployee_ID' => $result['employee']->intEmployee_ID, 'txtStatus' => 'Dibuat']);

        // Set to Terkirim
        $result['voucher']->update(['txtStatus' => 'Terkirim']);

        // 2. Simulate Webhook WhatsApp Reply
        $response = $this->postJson('/api/whatsapp/webhook', [
            'sender' => '6281234567770',
            'message' => 'Diterima, terima kasih!',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => true, 'voucher_activated' => true]);

        $result['voucher']->refresh();
        $this->assertEquals('Aktif', $result['voucher']->txtStatus);
    }

    public function test_pos_checkout_with_voucher_and_split_bill(): void
    {
        $cashier = User::where('txtUsername', 'umkm_siti')->first();
        $this->actingAs($cashier);

        $employee = Employee::where('txtEmployeeNik', '22345')->first();
        $voucher = Voucher::where('intEmployee_ID', $employee->intEmployee_ID)->first();
        $merchant = $cashier->merchant;
        $product = Product::where('intMerchant_ID', $merchant->intMerchant_ID)->first();

        // 1. Check Voucher endpoint
        $checkRes = $this->postJson('/umkm/pos/check-voucher', ['code' => $voucher->txtVoucherCode]);
        $checkRes->assertStatus(200);
        $checkRes->assertJson(['valid' => true, 'employee_name' => $employee->txtEmployeeName]);

        // 2. Checkout sale that exceeds voucher balance (Split bill)
        // Set voucher balance to 20,000, buy item worth 25,000 -> cash needed = 5,000
        $voucher->update(['floatCurrentBalance' => 20000]);

        $checkoutRes = $this->postJson('/umkm/pos/checkout', [
            'items' => [['id' => $product->intProduct_ID, 'quantity' => 1]],
            'voucher_code' => $voucher->txtVoucherCode,
            'cash_given' => 10000,
        ]);

        $checkoutRes->assertStatus(200);
        $checkoutRes->assertJson([
            'success' => true,
            'sale' => [
                'payment_type' => 'SPLIT',
                'voucher_amount' => 20000,
                'cash_amount' => 5000,
                'change_amount' => 5000,
                'voucher_remaining' => 0,
            ],
        ]);

        $voucher->refresh();
        $this->assertEquals(0, $voucher->floatCurrentBalance);
        $this->assertEquals('Habis', $voucher->txtStatus);
    }

    public function test_umkm_claim_reimbursement_and_admin_approval(): void
    {
        $cashier = User::where('txtUsername', 'umkm_siti')->first();
        $this->actingAs($cashier);

        // Submit claim
        $claimRes = $this->post('/umkm/claims', [
            'bank_name' => 'BCA',
            'bank_account_number' => '8830123456',
            'bank_account_holder' => 'Siti Rahayu',
        ]);

        $claimRes->assertSessionHas('success');
        $this->assertDatabaseHas('trClaimReimbursement', [
            'intMerchant_ID' => $cashier->intMerchant_ID,
            'txtStatus' => 'Diajukan',
        ]);

        // Login as Admin & Approve
        $admin = User::where('txtUsername', 'admin')->first();
        $this->actingAs($admin);

        $claim = \App\Models\ClaimReimbursement::where('intMerchant_ID', $cashier->intMerchant_ID)->first();
        $approveRes = $this->post("/admin/claims/{$claim->intClaim_ID}/approve");
        $approveRes->assertSessionHas('success');

        $claim->refresh();
        $this->assertEquals('Disetujui', $claim->txtStatus);

        // Mark as Paid
        $payRes = $this->post("/admin/claims/{$claim->intClaim_ID}/pay");
        $payRes->assertSessionHas('success');

        $claim->refresh();
        $this->assertEquals('Dibayar', $claim->txtStatus);
    }

    public function test_number_separator_inputs_are_sanitized_and_stored_correctly(): void
    {
        // 1. Admin creates voucher with separator amount "75.000"
        $admin = User::where('txtUsername', 'admin')->first();
        $this->actingAs($admin);

        $resVoucher = $this->post('/admin/vouchers/single', [
            'nik' => '99988',
            'name' => 'Karyawan Separator Test',
            'phone' => '6281234567899',
            'amount' => '75.000',
            'department' => 'Finance',
            'send_whatsapp' => 0,
        ]);
        $resVoucher->assertSessionHas('success');
        $this->assertDatabaseHas('mVoucher', [
            'floatInitialBalance' => 75000,
            'floatCurrentBalance' => 75000,
        ]);

        // 2. UMKM creates product with separator price "35.000" and stock "150"
        $cashier = User::where('txtUsername', 'umkm_siti')->first();
        $this->actingAs($cashier);

        $resProd = $this->post('/umkm/products', [
            'product_name' => 'Menu Separator Test',
            'price' => '35.000',
            'stock' => '150',
            'unit' => 'Porsi',
        ]);
        $resProd->assertSessionHas('success');
        $this->assertDatabaseHas('mProduct', [
            'txtProductName' => 'Menu Separator Test',
            'floatPrice' => 35000,
            'intStock' => 150,
        ]);

        // 3. POS checkout with formatted cash string "50.000"
        $product = Product::where('txtProductName', 'Menu Separator Test')->first();
        $checkoutRes = $this->postJson('/umkm/pos/checkout', [
            'items' => [['id' => $product->intProduct_ID, 'quantity' => 1]],
            'cash_given' => '50.000',
        ]);
        $checkoutRes->assertStatus(200);
        $checkoutRes->assertJson([
            'success' => true,
            'sale' => [
                'cash_amount' => 35000,
                'change_amount' => 15000,
            ],
        ]);
    }
}
