<?php

namespace App\Models;

use App\Models\Concerns\HasKmiAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory, HasKmiAudit;

    protected $table = 'mCategory';
    protected $primaryKey = 'intCategory_ID';

    const CREATED_AT = 'dtmInserted';
    const UPDATED_AT = 'dtmUpdated';

    protected $fillable = [
        'intMerchant_ID',
        'txtCategoryName',
        'txtCategorySlug',
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

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class, 'intMerchant_ID', 'intMerchant_ID');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'intCategory_ID', 'intCategory_ID');
    }
}
