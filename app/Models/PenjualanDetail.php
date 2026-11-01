<?php

namespace App\Models;

use App\Models\Concerns\HasKmiAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenjualanDetail extends Model
{
    use HasFactory, HasKmiAudit;

    protected $table = 'trPenjualan_Details';
    protected $primaryKey = 'intPenjualan_Detail_ID';

    const CREATED_AT = 'dtmInserted';
    const UPDATED_AT = null;

    protected $fillable = [
        'intPenjualan_ID',
        'intProduct_ID',
        'txtProductName',
        'floatPrice',
        'intQuantity',
        'floatSubtotal',
        'txtInsertedBy',
        'dtmInserted',
    ];

    protected function casts(): array
    {
        return [
            'floatPrice' => 'float',
            'intQuantity' => 'integer',
            'floatSubtotal' => 'float',
            'dtmInserted' => 'datetime',
        ];
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Penjualan::class, 'intPenjualan_ID', 'intPenjualan_ID');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'intProduct_ID', 'intProduct_ID');
    }
}

