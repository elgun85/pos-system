<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Settings extends Model
{

    protected $fillable =
    [

        'logo',
        'app_name',
        'name_store',
        'vat',
        'phone',
        'address',
        'website',
        'support_email',
        'receipt_header',
        'receipt_footer',
        'receipt_show_logo',
        'receipt_show_phone',
        'receipt_show_address',
        'receipt_show_vat',
        'currency_code',
        'currency_symbol',
        'tax_enabled',
        'tax_rate',

    ];

    protected $casts =
    [
        'receipt_show_logo'    => 'boolean',
        'receipt_show_phone'   => 'boolean',
        'receipt_show_address' => 'boolean',
        'receipt_show_vat'     => 'boolean',
        'tax_enabled'          => 'boolean',
        'tax_rate'             => 'decimal:2',
    ];

    public static function current(): self
    {
        $attributes = Cache::rememberForever('app_settings', function () {
            $row = static::query()->first() ?? static::create([])->refresh();

            return $row->getAttributes(); // sadə massiv, serialize problemi yoxdur
        });

        return (new static)->newFromBuilder($attributes);
    }

    protected static function booted(): void
    {
        static::saved(fn() => Cache::forget('app_settings'));
        static::deleted(fn() => Cache::forget('app_settings'));
    }
}
