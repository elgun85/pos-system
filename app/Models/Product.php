<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'image',
        'category_id',
        'brand_id',
        'supplier_id',
        'sku',
       // 'barcode',
        'cost_price',
        'sale_price',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class)->where('status', true)->where('parent_id', '!=', null);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    public function returnItems()
    {
        return $this->hasMany(ReturnItem::class);
    }
    public function saleItems()
    {
        return $this->hasMany(SalesItem::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }


            protected static function booted(): void
    {
        static::updating(function ($model) {

            if ($model->isDirty('image') && $model->getOriginal('image')) {

                Storage::disk('public')->delete($model->getOriginal('image'));
            }
        });

        static::deleting(function ($model) {
            if ($model->image) {
                Storage::disk('public')->delete($model->image);
            }
        });
    }
}
