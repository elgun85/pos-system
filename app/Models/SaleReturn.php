<?php

namespace App\Models;

use App\Models\ReturnItem;
use Illuminate\Database\Eloquent\Model;

class SaleReturn extends Model
{
    protected $table = 'returns'; // Cədvəl adı returns qala bilər
    protected $fillable = [
        'sale_id',
        'quantity',
        'total',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function returnItems()
    {
        return $this->hasMany(ReturnItem::class);
    }
}
