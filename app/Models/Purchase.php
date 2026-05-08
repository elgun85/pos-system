<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'photo',
        'invoice_number',
        'supplier_id',
        'total_price',
        'status',
    ];



    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
