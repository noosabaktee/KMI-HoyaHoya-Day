<?php

namespace App\Models;

use App\Models\Concerns\HasKmiAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasKmiAudit;

    protected $table = 'mUser';
    protected $primaryKey = 'intUser_ID';

    const CREATED_AT = 'dtmInserted';
    const UPDATED_AT = 'dtmUpdated';

    protected $fillable = [
        'txtUsername',
        'txtFullName',
        'txtEmail',
        'txtPassword',
        'txtRole',
        'intMerchant_ID',
        'txtPhone',
        'txtInsertedBy',
        'txtUpdatedBy',
        'bitActive',
    ];

    protected $hidden = [
        'txtPassword',
        'txtRememberToken',
    ];

    protected function casts(): array
    {
        return [
            'txtPassword' => 'hashed',
            'bitActive' => 'integer',
            'dtmInserted' => 'datetime',
            'dtmUpdated' => 'datetime',
        ];
    }

    public function getAuthPassword(): string
    {
        return (string) $this->txtPassword;
    }

    public function getAuthIdentifierName(): string
    {
        return 'intUser_ID';
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class, 'intMerchant_ID', 'intMerchant_ID');
    }

    public function isAdmin(): bool
    {
        return $this->txtRole === 'admin';
    }

    public function isUmkm(): bool
    {
        return $this->txtRole === 'umkm';
    }
}
