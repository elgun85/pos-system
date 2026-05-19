<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name',
        'description',
        'icon',
        'status',
    ];


        protected static function booted(): void
    {
        static::updating(function ($model) {

            if ($model->isDirty('icon') && $model->getOriginal('icon')) {

                Storage::disk('public')->delete($model->getOriginal('icon'));
            }
        });

        static::deleting(function ($model) {
            if ($model->icon) {
                Storage::disk('public')->delete($model->icon);
            }
        });
    }
}
