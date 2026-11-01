<?php

namespace App\Models;

use App\Models\Concerns\HasKmiAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogActivity extends Model
{
    use HasFactory, HasKmiAudit;

    protected $table = 'logActivity';
    protected $primaryKey = 'intActivity_ID';

    const CREATED_AT = 'dtmInserted';
    const UPDATED_AT = 'dtmUpdated';

    protected $fillable = [
        'txtActivityType',
        'txtDescription',
        'txtIpAddress',
        'intUser_ID',
        'txtInsertedBy',
        'txtUpdatedBy',
    ];

    protected function casts(): array
    {
        return [
            'dtmInserted' => 'datetime',
            'dtmUpdated' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'intUser_ID', 'intUser_ID');
    }

    public static function record(string $type, string $description, ?int $userId = null): void
    {
        try {
            static::create([
                'txtActivityType' => $type,
                'txtDescription' => $description,
                'txtIpAddress' => request()->ip(),
                'intUser_ID' => $userId ?? auth()->id(),
            ]);
        } catch (\Throwable $e) {
            // Ignore logging errors to not break primary operations
        }
    }
}

