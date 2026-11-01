<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Auth;

trait HasKmiAudit
{
    public static function bootHasKmiAudit(): void
    {
        static::creating(function ($model) {
            $user = Auth::user();
            $username = $user ? ($user->txtUsername ?? $user->name ?? 'SYSTEM') : 'SYSTEM';

            if (empty($model->txtInsertedBy)) {
                $model->txtInsertedBy = $username;
            }

            // If the model has txtUpdatedBy
            if (array_key_exists('txtUpdatedBy', $model->getAttributes()) || in_array('txtUpdatedBy', $model->getFillable())) {
                if (empty($model->txtUpdatedBy)) {
                    $model->txtUpdatedBy = $username;
                }
            }

            if (array_key_exists('bitActive', $model->getAttributes()) || in_array('bitActive', $model->getFillable())) {
                if (!isset($model->bitActive)) {
                    $model->bitActive = 1;
                }
            }
        });

        static::updating(function ($model) {
            $user = Auth::user();
            $username = $user ? ($user->txtUsername ?? $user->name ?? 'SYSTEM') : 'SYSTEM';

            if (array_key_exists('txtUpdatedBy', $model->getAttributes()) || in_array('txtUpdatedBy', $model->getFillable())) {
                $model->txtUpdatedBy = $username;
            }
        });
    }
}

