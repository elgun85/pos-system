<?php

namespace App\Services;

use App\Models\Damage;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DamageService
{

    public function createDamage(array $data): Damage
    {
        return DB::transaction(function () use ($data) {

            $productId = $data['product_id'];
            $quantity = $data['quantity'];


            $inventory = Inventory::where('product_id', $productId)
                ->lockForUpdate()
                ->first();

            if (!$inventory || $inventory->quantity < $quantity) {
                throw ValidationException::withMessages(['data.quantity' => __('resource.no_inventory.error')]);
            }

            $product = Product::findOrFail($productId);
            $costPrice = (float) ($product->cost_price ?? 0);
            $totalCost = $quantity * $costPrice;

            $damage = Damage::create([
                'product_id'    =>      $productId,
                'user_id'      =>      $data['user_id'] ?? auth()->id(),
                'quantity'     =>      $quantity,
                'cost_price'   =>      $costPrice,
                'total_cost'   =>      $totalCost,
                'notes'        =>      $data['notes'] ?? null,
            ]);

            $inventory->decrement('quantity', $quantity);

            return $damage;
        });
    }


    public function updateDamage(Damage $damage, array $data): Damage
    {
        return DB::transaction(function () use ($damage, $data) {
            $oldQuantity = $damage->quantity;
            $newQuantity = $data['quantity'];

            $difference  = $newQuantity - $oldQuantity;

            $inventory = Inventory::where('product_id', $damage->product_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($difference > 0 && $inventory->quantity < $difference) {
                 throw ValidationException::withMessages(['data.quantity' =>__('resource.no_inventory.error')]);

            }

            if ($difference > 0) {
                $inventory->decrement('quantity', $difference);
            } elseif ($difference < 0) {
                $inventory->increment('quantity', abs($difference));
            }

            $costPrice = (float) $damage->cost_price;

            $totalCost = $newQuantity * $costPrice;

            $damage->update([
                'quantity' => $newQuantity,
                'total_cost' => $totalCost,
                'notes' => $data['notes'] ?? null,
            ]);

            return $damage->refresh();
        });
    }

    public function deleteDamage(Damage $damage): void
    {
        DB::transaction(function () use ($damage) {
            $inventory = Inventory::where('product_id', $damage->product_id)
                ->lockForUpdate()
                ->firstOrFail();

                $inventory->increment('quantity', $damage->quantity);

                $damage->delete();
        });
    }
}
