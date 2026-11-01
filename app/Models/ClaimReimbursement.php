<?php

namespace App\Models;

use App\Models\Concerns\HasKmiAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClaimReimbursement extends Model
{
    use HasFactory, HasKmiAudit;

    protected $table = 'trClaimReimbursement';
    protected $primaryKey = 'intClaim_ID';

    const CREATED_AT = 'dtmInserted';
    const UPDATED_AT = null;

    protected $fillable = [
        'txtClaimNumber',
        'intMerchant_ID',
        'floatTotalClaimAmount',
        'intTotalTransactions',
        'txtBankName',
        'txtBankAccountNumber',
        'txtBankAccountHolder',
        'txtStatus',
        'txtRejectionReason',
        'txtReceiptFile',
        'dtmSubmittedAt',
        'dtmApprovedAt',
        'dtmPaidAt',
        'txtNotes',
        'txtInsertedBy',
        'dtmInserted',
    ];

    protected function casts(): array
    {
        return [
            'floatTotalClaimAmount' => 'float',
            'intTotalTransactions' => 'integer',
            'dtmSubmittedAt' => 'datetime',
            'dtmApprovedAt' => 'datetime',
            'dtmPaidAt' => 'datetime',
            'dtmInserted' => 'datetime',
        ];
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class, 'intMerchant_ID', 'intMerchant_ID');
    }

    public function details(): HasMany
    {
        return $this->hasMany(ClaimReimbursementDetail::class, 'intClaim_ID', 'intClaim_ID');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Penjualan::class, 'intClaim_ID', 'intClaim_ID');
    }
}

