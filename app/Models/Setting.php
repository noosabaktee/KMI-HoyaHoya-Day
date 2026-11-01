<?php

namespace App\Models;

use App\Models\Concerns\HasKmiAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory, HasKmiAudit;

    protected $table = 'mSetting';
    protected $primaryKey = 'intSetting_ID';

    const CREATED_AT = 'dtmInserted';
    const UPDATED_AT = 'dtmUpdated';

    protected $fillable = [
        'txtSettingKey',
        'txtSettingValue',
        'txtDescription',
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

    public static function getVal(string $key, $default = null): ?string
    {
        $setting = static::where('txtSettingKey', $key)->where('bitActive', 1)->first();
        return $setting ? $setting->txtSettingValue : $default;
    }

    public static function setVal(string $key, ?string $value, ?string $description = null): void
    {
        static::updateOrCreate(
            ['txtSettingKey' => $key],
            [
                'txtSettingValue' => $value,
                'txtDescription' => $description,
                'bitActive' => 1,
            ]
        );
    }
}

