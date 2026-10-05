<?php

namespace App\Models;

use App\Enums\PaymentMethodCode;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'icon',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'code' => PaymentMethodCode::class,
            'status' => 'boolean',
        ];
    }

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
        return static::where('code', PaymentMethodCode::CASH)
            ->where('status', true)
            ->value('id');
    }

    public static function cardId(): ?int
    {
        return static::where('code', PaymentMethodCode::CARD)
            ->where('status', true)
            ->value('id');
    }

    public static function creditId(): ?int
    {
        return static::where('code', PaymentMethodCode::CREDIT)
            ->where('status', true)
            ->value('id');
    }

    public static function getIdByCode(PaymentMethodCode $code): ?int
    {
        return static::where('code', $code)
            ->where('status', true)
            ->value('id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
