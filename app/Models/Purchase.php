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
    
    public function stockBlockMessage(): ?string
    {
        if (! (bool) $this->status) {
            return null;
        }

        $grouped = $this->purchaseItems()->with('product')->get()->groupBy('product_id');

        foreach ($grouped as $productId => $rows) {
            $purchased = (float) $rows->sum('quantity');
            $available = (float) (Inventory::where('product_id', $productId)->value('quantity') ?? 0);

            if ($available < $purchased) {
                return __('resource.purchase.stock_detail', [
                    'name'      => $rows->first()->product?->name ?? ('#' . $productId),
                    'purchased' => $purchased,
                    'available' => $available,
                ]);
            }
        }

        return null;
    }
    
}
