<?php

namespace App\Models;

use App\Models\Concerns\HasKmiAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory, HasKmiAudit;

    protected $table = 'mEmployee';
    protected $primaryKey = 'intEmployee_ID';

    const CREATED_AT = 'dtmInserted';
    const UPDATED_AT = 'dtmUpdated';

    protected $fillable = [
        'txtEmployeeNik',
        'txtEmployeeName',
        'txtEmployeePhone',
        'txtDepartment',
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

    public function voucher(): HasOne
    {
        return $this->hasOne(Voucher::class, 'intEmployee_ID', 'intEmployee_ID');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Penjualan::class, 'intEmployee_ID', 'intEmployee_ID');
    }

    public function whatsAppLogs(): HasMany
    {
        return $this->hasMany(LogWhatsApp::class, 'intEmployee_ID', 'intEmployee_ID');
    }
}

