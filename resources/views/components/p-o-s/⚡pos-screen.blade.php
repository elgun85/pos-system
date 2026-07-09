<?php

use Livewire\Component;
use App\Models\Product;
use App\Models\Inventory;
use Livewire\Attributes\Computed;
use Filament\Notifications\Notification;
use App\Models\PaymentMethod;
use App\Models\Sale;
use App\Models\SalesItem;
use Illuminate\Support\Facades\DB;
use App\Services\SalesService;


new class extends Component {
    public $search = '';
    public $cart = [];
    public $products;
    public $customers;
    public $paymentMethods;

    //properties for checkout
    public $customer_id = null;
    public $payment_method_id;
    public $paid_amount = 0;
    public $discount_amount = 0;

    public function mount()
    {
        $this->products = Product::where('status', true)->get();
        $this->paymentMethods = PaymentMethod::where('status', true)->orderByRaw("CASE WHEN name = 'Nəğd' THEN 0 ELSE 1 END")->orderBy('name')->get();
        $this->payment_method_id = $this->paymentMethods->first()?->id;
    }

    #[Computed]
    public function filteredProducts()
    {
        if (blank($this->search)) {
            return Product::query()->latest()
            ->with('brand:id,name')
            ->activeProduct()->favoriteProduct()
            ->minimumStock()
            ->limit(30)
            ->get();
        }

        return Product::query()
            ->with('brand:id,name')

           ->select(['id','brand_id','name', 'sku', 'sale_price'])
            ->where(function ($query) {
                $query->where('name', 'like', $this->search . '%');
            })
            ->activeProduct()
            ->limit(50)
            ->get();
    }

    public function updatedSearch()
    {
        $search = trim($this->search);
        if (blank($search)) {
            return;
        }
        $product = Product::where('sku', $search)->first();
        if ($product) {
            $this->addToCart($product->id);
            $this->search = '';
        }
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);

        $inventory = Inventory::where('product_id', $productId)->first();
        if (!$inventory || $inventory->quantity < 0) {
            Notification::make()->title('Bu məhsulun stokda kifayət qədər miqdarı yoxdur.')->danger()->send();
            return;
        }

        if (isset($this->cart[$productId])) {
            $currentQuantity = $this->cart[$productId]['quantity'];

            if ($currentQuantity >= $inventory->quantity) {
                Notification::make()->title('Bu məhsulun stokda kifayət qədər miqdarı yoxdur.')->danger()->send();
                return;
            }
            $this->cart[$productId]['quantity']++;
        } else {
            $this->cart[$productId] = [
                'product_id' => $productId,
                'name' => $product->name,
                'brand' => $product->brand?->name,
                'sku' => $product->sku,
                'sale_price' => $product->sale_price,
                'quantity' => 1,
            ];
        }
    }

    public function removeFromCart($productId)
    {
        if (isset($this->cart[$productId])) {
            $removedItem = $this->cart[$productId];
            unset($this->cart[$productId]);
            Notification::make()
                ->title("{$removedItem['name']} səbətdən silindi.")
                ->warning()
                ->send();
        }
    }

    #[Computed]
    public function subtotal(): float
    {
        return collect($this->cart)->sum(fn($item) => (float) $item['sale_price'] * (int) $item['quantity']);
    }

    #[Computed]
    public function total(): float
    {
        $subtotal = $this->subtotal;
        $discount = (float) $this->discount_amount;
        return max($subtotal - $discount, 0);
    }

    #[Computed]
    public function change(): float
    {
        // Əgər istifadəçi xananı tam silibsə, hesablamada xəta olmasın deyə float-a çeviririk
        $paid = (float) $this->paid_amount;

        // total dəyişəninizin də mövcudluğunu float olaraq yoxlayın
        $total = (float) ($this->total ?? 0);

        if ($paid >= $total) {
            return $paid - $total;
        }

        return 0;
    }

    public function checkout(SalesService $salesService)
    {
// 1. Validasiya
    $this->validate([
        'payment_method_id' => 'required|exists:payment_methods,id'
    ], [
        'payment_method_id.required' => 'Ödəniş növü seçimi vacibdir.'
    ]);

try {
        // 2. Satış servisini çağırırıq (Bütün DB və Stok işlərini o həll edir)
        $sale = $salesService->createSale($this->cart, [
            'total'           => $this->total,
            'paid_amount'     => $this->paid_amount,
            'payment_method_id'=> $this->payment_method_id,
            'discount_amount' => $this->discount_amount,
        ]);



            
          
            $this->cart = [];
            $this->search = '';
            $this->paid_amount = 0;
            $this->discount_amount = 0;
            $this->discount_amount = 0;

// 4. Uğurlu bildiriş göndərilir
        Notification::make()->title('Satış uğurla tamamlandı.')->success()->send();

// 5. Çap pəncərəsi açılır
        $printUrl = route('sales.print', ['sale' => $sale->id]);
        $this->js("window.open('{$printUrl}', '_blank', 'width=400,height=600');");

            //     $this->js('window.dispatchEvent(new CustomEvent("print-receipt", { detail: { url: "' . route('sales.print', ['sale' => $sale->id]) . '" } }))');
        } catch (\Exception $e) {
            Notification::make()
                ->title('Satış zamanı xəta baş verdi: ' . $e->getMessage())
                ->danger()
                ->send();
            return;
        }
    }
};
?>
<div>

    <div class="h-[calc(100vh-120px)]">
        <div class="grid h-full grid-cols-[1fr_450px] gap-4">

            <!-- CHECKOUT -->
            <flux:card class="flex h-full flex-col">

                <div class="border-b     
                  border-zinc-200  pb-4">
                    <div class="flex items-center justify-between">

                        <h2 class="text-2xl font-bold">
                            Səbət
                        </h2>

                        <span class="rounded-full bg-primary-100 px-3 py-1 text-xs font-medium">
                            {{ count($this->cart) }} məhsul
                        </span>

                    </div>
                </div>

                <!-- CART ITEMS -->
                <div class="flex-1 space-y-2 overflow-y-auto py-4">
                    @forelse($this->cart as $cartItem)
                        <div
                            class="flex items-center rounded-lg border
                     border-gray-200 bg-gray-50 p-3">

                            <div class="flex-1 font-medium truncate">
                                {{ $cartItem['name'] }} / {{ $cartItem['brand'] }}
                                <span class="text-xs text-zinc-500">
                                    (SKU: {{ $cartItem['sku'] }})
                                </span>
                            </div>

                            <div class="mx-4 w-14">
                                <input type="number" value="{{ $cartItem['quantity'] }}" min="1"
                                    wire:model.live.debounce.500ms="cart.{{ $cartItem['product_id'] }}.quantity"
                                    class="w-full border-0 bg-transparent text-center focus:ring-0">
                            </div>

                            <div class="w-40 text-right text-sm font-medium text-zinc-600">
                                {{ $cartItem['quantity'] }} x
                                ₼{{ number_format($cartItem['sale_price'], 2) }} =

                                ₼{{ number_format(((int) ($cartItem['quantity'] ?: 0)) * ((float) ($cartItem['sale_price'] ?: 0)), 2) }}

                            </div>

                            <div class="ml-8">
                                <flux:button size="sm" variant="danger"
                                    wire:click="removeFromCart({{ $cartItem['product_id'] }})">
                                    ✕
                                </flux:button>
                            </div>

                        </div>
                    @empty
                        <div class="text-center text-red-500">
                            Səbət boşdur
                        </div>
                    @endforelse

                </div>

                <!-- TOTAL -->
                <div class="border-t pt-4 space-y-2">

                    <div class="flex justify-between">
                        <span>Cəmi</span>
                        <span>₼{{ number_format($this->subtotal, 2) }}</span>
                    </div>

                    <div class="flex justify-between text-red-500">
                        <span>Endirim</span>


                        <input type="number" min="0" step="0.01"
                            wire:model.live.debounce.500ms="discount_amount"
                            class="w-24 border-0 bg-transparent text-right focus:ring-0">

                    </div>

                    <div class="flex justify-between  ">
                        <span>Ödəniş növü</span>


                        <flux:select wire:model="payment_method_id" placeholder="Ödəniş növü seçin" class="w-48">
                            @foreach ($this->paymentMethods as $method)
                                <flux:select.option :value="$method->id">
                                    {{ $method->name }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                        @error('payment_method_id')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror

                    </div>

                    <div class="flex justify-between border-t pt-3 text-xl font-bold">

                        <span>Yekun</span>
                        <span>₼{{ number_format($this->total, 2) }}</span>

                    </div>

                    <div class="flex justify-between">
                        <span>Qaytarılan məbləğ</span>
                        <span>₼{{ number_format($this->change, 2) }}</span>
                    </div>



                    <flux:input placeholder="Ödənilən məbləğ" wire:model.live.debounce.500ms="paid_amount"
                        type="number" step="0.01" min="0" />

                    <flux:button wire:click="checkout" wire:loading.attr="disabled" variant="primary" class="w-full">
                        Satışı Tamamla
                    </flux:button>

                </div>

            </flux:card>

            <!-- PRODUCTS -->
            <flux:card class="flex h-full flex-col">

                <div class="space-y-4">
                    <h2 class="text-2xl font-bold">
                        Məhsullar
                    </h2>
                    <flux:input autofocus wire:model.live="search" placeholder="Məhsul axtar..." />
                    @if (session()->has('error'))
                        <div
                            class="mt-2 p-4 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded-lg shadow-md">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if (session()->has('success'))
                        <div
                            class="mt-2 p-4 bg-green-100 dark:bg-green-900
                             text-green-700 dark:text-green-300 rounded-lg shadow-md">
                            {{ session('success') }}
                        </div>
                    @endif
                </div>



                {{-- PRODUCT LIST --}}
                <div class="mt-4 overflow-y-auto h-[700px]">

                    <div class="grid grid-cols-3 gap-4 content-start">

                        @forelse($this->filteredProducts as $product)
                            <div wire:click="addToCart({{ $product->id }})"
                                class="cursor-pointer overflow-hidden rounded-lg border bg-zinc-100 hover:shadow-md min-h-[80px]">

                                <div class="p-1 flex-1 flex flex-col justify-between ">

                                    <div class="font-normal  truncate text-base">
                                        {{ $product->name }}
                                    </div>

                                    <div class="text-xs text-zinc-500">
                                        {{ $product->brand?->name }}
                                    </div>

                                    <div class="mt-1 text-xs text-zinc-500">
                                        Stok: {{ $product->inventory ? $product->inventory->quantity : 'Yoxdur' }}
                                    </div>
                                    <div class="text-xs text-zinc-500">
                                        {{ $product->sku }}
                                    </div>

                                    <div class="mt-1 text-xm font-bold">
                                        ₼ {{ number_format($product->sale_price, 2) }}
                                    </div>
                                </div>

                            </div>
                        @empty
                            <div class="text-center py-10 text-red-500 col-span-3">
                                Məhsul tapılmadı
                            </div>
                        @endforelse

                    </div>

                </div>

            </flux:card>



        </div>
    </div>

    <script>
        window.addEventListener('print-receipt', event => {
            const printUrl = event.detail.url;
            const printWindow = window.open(printUrl, '_blank', 'width=800,height=600');
            printWindow.focus();
            printWindow.print();
        });
    </script>


</div>
