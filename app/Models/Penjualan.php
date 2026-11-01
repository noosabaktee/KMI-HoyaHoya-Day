<?php

namespace App\Models;

use App\Models\Concerns\HasKmiAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Penjualan extends Model
{
    use HasFactory, HasKmiAudit;

    protected $table = 'trPenjualan';
    protected $primaryKey = 'intPenjualan_ID';

    const CREATED_AT = 'dtmInserted';
    const UPDATED_AT = null;

    protected $fillable = [
        'txtInvoiceNumber',
        'intMerchant_ID',
        'intUser_ID',
        'intEmployee_ID',
        'intVoucher_ID',
        'floatTotalAmount',
        'floatVoucherAmount',
        'floatCashAmount',
        'floatChangeAmount',
        'txtPaymentType',
        'txtNotes',
        'bitIsClaimed',
        'intClaim_ID',
        'txtInsertedBy',
        'dtmInserted',
    ];

    protected function casts(): array
    {
        return [
            'floatTotalAmount' => 'float',
            'floatVoucherAmount' => 'float',
            'floatCashAmount' => 'float',
            'floatChangeAmount' => 'float',
            'bitIsClaimed' => 'integer',
            'dtmInserted' => 'datetime',
        ];
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class, 'intMerchant_ID', 'intMerchant_ID');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'intUser_ID', 'intUser_ID');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'intEmployee_ID', 'intEmployee_ID');
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class, 'intVoucher_ID', 'intVoucher_ID');
    }

    public function claim(): BelongsTo
    {
        return $this->belongsTo(ClaimReimbursement::class, 'intClaim_ID', 'intClaim_ID');
    }

    public function details(): HasMany
    {
        return $this->hasMany(PenjualanDetail::class, 'intPenjualan_ID', 'intPenjualan_ID');
    }

    public function voucherUsage(): HasOne
    {
        return $this->hasOne(VoucherUsage::class, 'intPenjualan_ID', 'intPenjualan_ID');
    }
}

