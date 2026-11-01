<?php

namespace App\Models;

use App\Models\Concerns\HasKmiAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogWhatsApp extends Model
{
    use HasFactory, HasKmiAudit;

    protected $table = 'logWhatsApp';
    protected $primaryKey = 'intWhatsApp_ID';

    const CREATED_AT = 'dtmInserted';
    const UPDATED_AT = 'dtmUpdated';

    protected $fillable = [
        'txtRecipientNumber',
        'txtMessage',
        'txtDirection',
        'txtMediaUrl',
        'txtStatus',
        'txtApiResponse',
        'intEmployee_ID',
        'intVoucher_ID',
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

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'intEmployee_ID', 'intEmployee_ID');
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class, 'intVoucher_ID', 'intVoucher_ID');
    }
}

