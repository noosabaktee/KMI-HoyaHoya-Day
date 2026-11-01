<?php

namespace App\Models;

use App\Models\Concerns\HasKmiAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoucherUsage extends Model
{
    use HasFactory, HasKmiAudit;

    protected $table = 'trVoucherUsage';
    protected $primaryKey = 'intVoucherUsage_ID';

    const CREATED_AT = 'dtmInserted';
    const UPDATED_AT = null;

    protected $fillable = [
        'intVoucher_ID',
        'intPenjualan_ID',
        'floatAmountUsed',
        'floatBalanceBefore',
        'floatBalanceAfter',
        'txtInsertedBy',
        'dtmInserted',
    ];

    protected function casts(): array
    {
        return [
            'floatAmountUsed' => 'float',
            'floatBalanceBefore' => 'float',
            'floatBalanceAfter' => 'float',
            'dtmInserted' => 'datetime',
        ];
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class, 'intVoucher_ID', 'intVoucher_ID');
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Penjualan::class, 'intPenjualan_ID', 'intPenjualan_ID');
    }
}

