<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Brand extends Model
{
    protected $fillable = [
        'name',
        'logo',
        'status',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    protected static function booted()
    {
        static::updating(function ($brand) {
            // Əgər logo sahəsi dəyişibsə və köhnə logo varsa, onu sil
            if ($brand->isDirty('logo') && $brand->getOriginal('logo')) {
                Storage::disk('public')->delete($brand->getOriginal('logo'));
            }
        });

        static::deleting(function ($brand) {
            // Brend silinəndə şəkli də sil
            if ($brand->logo) {
                Storage::disk('public')->delete($brand->logo);
            }
        });
    }
}
