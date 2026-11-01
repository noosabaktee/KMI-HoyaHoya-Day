<?php

namespace App\Models;

use App\Models\Concerns\HasKmiAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClaimReimbursementDetail extends Model
{
    use HasFactory, HasKmiAudit;

    protected $table = 'trClaimReimbursement_Details';
    protected $primaryKey = 'intClaim_Detail_ID';

    const CREATED_AT = 'dtmInserted';
    const UPDATED_AT = null;

    protected $fillable = [
        'intClaim_ID',
        'intPenjualan_ID',
        'floatVoucherAmount',
        'txtInsertedBy',
        'dtmInserted',
    ];

    protected function casts(): array
    {
        return [
            'floatVoucherAmount' => 'float',
            'dtmInserted' => 'datetime',
        ];
    }

    public function claim(): BelongsTo
    {
        return $this->belongsTo(ClaimReimbursement::class, 'intClaim_ID', 'intClaim_ID');
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Penjualan::class, 'intPenjualan_ID', 'intPenjualan_ID');
    }
}

