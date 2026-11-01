<?php

namespace App\Models;

use App\Models\Concerns\HasKmiAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Merchant extends Model
{
    use HasFactory, HasKmiAudit;

    protected $table = 'mMerchant';
    protected $primaryKey = 'intMerchant_ID';

    const CREATED_AT = 'dtmInserted';
    const UPDATED_AT = 'dtmUpdated';

    protected $fillable = [
        'txtMerchantCode',
        'txtMerchantName',
        'txtOwnerName',
        'txtPhone',
        'txtBoothNumber',
        'txtCategory',
        'txtDescription',
        'txtBankName',
        'txtBankAccountNumber',
        'txtBankAccountHolder',
        'txtInsertedBy',
        'txtUpdatedBy',
        'bitActive',
    ];

    protected function casts(): array
    {
        return [
            'bitActive' => 'integer',
            'dtmInserted' => 'datetime',
            'dtmUpdated' => 'datetime',
        ];
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'intMerchant_ID', 'intMerchant_ID');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'intMerchant_ID', 'intMerchant_ID');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class, 'intMerchant_ID', 'intMerchant_ID');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Penjualan::class, 'intMerchant_ID', 'intMerchant_ID');
    }

    public function claims(): HasMany
    {
        return $this->hasMany(ClaimReimbursement::class, 'intMerchant_ID', 'intMerchant_ID');
    }
}

