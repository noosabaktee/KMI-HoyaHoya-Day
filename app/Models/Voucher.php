<?php

namespace App\Models;

use App\Models\Concerns\HasKmiAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voucher extends Model
{
    use HasFactory, HasKmiAudit;

    protected $table = 'mVoucher';
    protected $primaryKey = 'intVoucher_ID';

    const CREATED_AT = 'dtmInserted';
    const UPDATED_AT = 'dtmUpdated';

    protected $fillable = [
        'intEmployee_ID',
        'txtVoucherCode',
        'floatInitialBalance',
        'floatCurrentBalance',
        'txtStatus',
        'txtBarcodeCardPath',
        'dtmSentAt',
        'dtmActivatedAt',
        'txtInsertedBy',
        'txtUpdatedBy',
        'bitActive',
    ];

    protected function casts(): array
    {
        return [
            'floatInitialBalance' => 'float',
            'floatCurrentBalance' => 'float',
            'bitActive' => 'integer',
            'dtmInserted' => 'datetime',
            'dtmUpdated' => 'datetime',
            'dtmSentAt' => 'datetime',
            'dtmActivatedAt' => 'datetime',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'intEmployee_ID', 'intEmployee_ID');
    }

    public function usages(): HasMany
    {
        return $this->hasMany(VoucherUsage::class, 'intVoucher_ID', 'intVoucher_ID');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Penjualan::class, 'intVoucher_ID', 'intVoucher_ID');
    }

    public function whatsAppLogs(): HasMany
    {
        return $this->hasMany(LogWhatsApp::class, 'intVoucher_ID', 'intVoucher_ID');
    }
}

