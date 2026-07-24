<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
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

    public function customerTransactions()
    {
        return $this->hasMany(CustomerTransaction::class);
    }

    protected function cashId(): ?int
    {
        return static::where('name', 'Nağd')
            ->where('status', true)
            ->value('id');
    }

    public static function cardId(): ?int
    {
        return static::where('name', 'Kart')
            ->where('status', true)
            ->value('id');
    }

    public static function creditId(): ?int
    {
        return static::where('name', 'Nisyə')
            ->where('status', true)
            ->value('id');
    }
}
