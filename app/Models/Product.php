<?php

namespace App\Models;

use App\Models\Concerns\HasKmiAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory, HasKmiAudit;

    protected $table = 'mProduct';
    protected $primaryKey = 'intProduct_ID';

    const CREATED_AT = 'dtmInserted';
    const UPDATED_AT = 'dtmUpdated';

    protected $fillable = [
        'intMerchant_ID',
        'intCategory_ID',
        'txtProductCode',
        'txtProductName',
        'floatPrice',
        'intStock',
        'txtUnit',
        'txtProductImage',
        'txtDescription',
        'txtInsertedBy',
        'txtUpdatedBy',
        'bitActive',
    ];

    protected function casts(): array
    {
        return [
            'floatPrice' => 'float',
            'intStock' => 'integer',
            'bitActive' => 'integer',
            'dtmInserted' => 'datetime',
            'dtmUpdated' => 'datetime',
        ];
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class, 'intMerchant_ID', 'intMerchant_ID');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'intCategory_ID', 'intCategory_ID');
    }

    public function saleDetails(): HasMany
    {
        return $this->hasMany(PenjualanDetail::class, 'intProduct_ID', 'intProduct_ID');
    }
}
