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
use App\Models\Customer;
use Flux\Flux;

new class extends Component {
    public $search = '';
    public $cart = [];
    public $products;
    public $customers;
    public $paymentMethods;

    //properties for checkout
    public $customer_id = null;
    public $customer_search = '';
    public $customerList = [];

    public $new_customer_name = '';
    public $new_customer_phone = '';
    public $new_customer_address = '';
    public $payment_method_id;
    public $paid_amount = '';
    public $discount_amount = 0;

    public function mount()
    {
        $this->products = Product::where('status', true)->get();
        $this->paymentMethods = PaymentMethod::where('status', true)->orderByRaw("CASE WHEN name = 'Nəğd' THEN 0 ELSE 1 END")->orderBy('name')->get();
        $this->payment_method_id = $this->paymentMethods->first()?->id;
        // $this->customers = Customer::orderBy('name')->get();
    }

    public function updatedCustomerSearch()
    {
        if (strlen($this->customer_search) < 2) {
            $this->customerList = [];
            return;
        }

        $this->customerList = Customer::query()
            ->select('id', 'name', 'phone', 'address')
            ->where('name', 'like', "%{$this->customer_search}%")
            ->orWhere('phone', 'like', "%{$this->customer_search}%")
            ->orWhere('address', 'like', "%{$this->customer_search}%")
            ->where('status', true)
            ->limit(20)
            ->get();
    }

    public function selectCustomer($id)
    {
        $customer = Customer::find($id);

        $this->customer_id = $customer->id;
        $this->customer_search = $customer->name;

        $this->customerList = [];
    }

    #[Computed]
    public function filteredProducts()
    {
        if (blank($this->search)) {
            return Product::query()->latest()->with('brand:id,name')->activeProduct()->favoriteProduct()->minimumStock()->limit(30)->get();
        }

        return Product::query()
            ->with('brand:id,name')

            ->select(['id', 'brand_id', 'name', 'sku', 'sale_price'])
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
    public function actualPaidAmount(): float
    {
        // Əgər input tamamilə boşdursa (null və ya boş string), deməli tam ödənişdir
        if ($this->paid_amount === '' || $this->paid_amount === null) {
            return (float) $this->total;
        }

        // Əgər kassir əllə "0" yazıbsa və ya hər hansı rəqəm daxil edibsə, onu qaytarırıq
        return (float) $this->paid_amount;
    }

    #[Computed]
    public function change(): float
    {
        $paid = $this->actualPaidAmount();
        $total = (float) ($this->total ?? 0);
        if ($paid >= $total) {
            return $paid - $total;
        }

        return 0;
    }

    #[Computed]
    public function deuAmount(): float
    {
        $paid = $this->actualPaidAmount;
        $total = (float) ($this->total ?? 0);

        if ($paid < $total) {
            return $total - $paid;
        }

        return 0;
    }

    public function checkout(SalesService $salesService)
    {
        // Yaranan nisyə borc məbləği
        $deuAmount = $this->deuAmount();

        // Əgər nisyə borc yaranırsa və müştəri seçilməyibsə, satışı dayandırırıq
        if ($deuAmount > 0 && !$this->customer_id) {
            Notification::make()->title('Nisyə satış üçün mütləq müştəri seçilməlidir!')->danger()->send();
            return;
        }

        $this->validate(
            [
                'payment_method_id' => 'required|exists:payment_methods,id',
            ],
            [
                'payment_method_id.required' => 'Ödəniş növü seçimi vacibdir.',
            ],
        );

        try {
            $payments = [];

            // Kassirin əllə daxil etdiyi və ya sistemin avtomatik tamamladığı məbləğ
            $totalToPay = $this->total;
            $paid = $this->actualPaidAmount;

            // Əgər müştəri artıq pul veribsə (məs: 16.70 əvəzinə 20 veribsə), kassaya daxil olan xalis məbləğ 16.70-dir
            $actualPaymentAmount = min($paid, $totalToPay);

            if ($actualPaymentAmount > 0) {
                $payments[] = [
                    'payment_method_id' => $this->payment_method_id,
                    'amount' => $actualPaymentAmount,
                ];
            }

            // 2. Satış servisini çağırırıq (Bütün DB və Stok işlərini o həll edir)
            $sale = $salesService->createSale($this->cart, [
                'total' => $totalToPay,
                'customer_id' => $this->customer_id,
                'payment_method_id' => $this->payment_method_id,
                'discount_amount' => $this->discount_amount,
                'payments' => $payments, // Massiv mütləq bura ötürülməlidir!
                'paid_amount' => $paid, // Həqiqi ödənilən məbləği ötürürük
            ]);

            $this->cart = [];
            $this->search = '';
            $this->paid_amount = '';
            $this->discount_amount = 0;
            $this->customer_id = null;
            $this->total = 0;
            $this->subtotal = 0;
            $this->customer_search = '';

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

    public function openCustomerModal()
    {
        $this->dispatch('open-customer-modal');
    }

    public function quickCreateCustomer()
    {
        $this->new_customer_phone = preg_replace('/\D/', '', $this->new_customer_phone);

        $this->validate(
            [
                'new_customer_name' => 'required|string|max:255',
                'new_customer_address' => 'nullable|string|max:255',
                'new_customer_phone' => ['nullable', 'regex:/^[0-9\s\-]+$/'],
            ],
            [
                'new_customer_name.required' => 'Müştərinin adı mütləq daxil edilməlidir.',
                'new_customer_name.max'      => 'Ad maksimum 255 simvol ola bilər.',
                'new_customer_address.max' =>   'Ünvan maksimum 255 simvol ola bilər.',
                'new_customer_phone.regex' =>   'Telefon nömrəsi yalnız rəqəmlərdən, boşluq və "-" işarəsindən ibarət ola bilər.',
            ],
        );

        try {
            $customer = Customer::create([
                'name' => $this->new_customer_name,
                'phone' => $this->new_customer_phone,
                'address' => $this->new_customer_address,
            ]);

            $this->new_customer_name = '';
            $this->new_customer_phone = '';
            $this->new_customer_address = '';

            $this->dispatch('close-customer-modal');
            session()->flash('success', 'Yeni müştəri uğurla əlavə edildi.');
        } catch (\Exception $e) {
            session()->flash('error', 'Xəta baş verdi: ' . $e->getMessage());
        }
    }
};
?>
<div x-data="{ openModal: false }" x-on:open-customer-modal.window="openModal = true"
    x-on:close-customer-modal.window="openModal = false">

    <div class="h-[calc(100vh-120px)]">
        <div class="grid h-full grid-cols-[1fr_450px] gap-4">

            <!-- CHECKOUT (Səbət tərəfi)-->
            <flux:card class="flex h-full flex-col">

                <div class="border-b border-zinc-200 pb-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-bold">Səbət</h2>
                        <span class="rounded-full bg-primary-100 px-3 py-1 text-xs font-medium">
                            {{ count($this->cart) }} məhsul
                        </span>
                        <div class="flex justify-between pt-3 text-xl font-bold">
                            <span class="font-bold text-red-600 text-4xl"> ₼{{ number_format($this->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- CART ITEMS -->
                <div class="flex-1 space-y-2 overflow-y-auto py-4">
                    @forelse($this->cart as $cartItem)
                        <div class="flex items-center rounded-lg border border-gray-200 bg-gray-50 p-3">
                            <div class="flex-1 font-medium truncate">
                                {{ $cartItem['name'] }} / {{ $cartItem['brand'] }}
                                <span class="text-xs text-zinc-500">(SKU: {{ $cartItem['sku'] }})</span>
                            </div>

                            <div class="mx-4 w-14">
                                <input type="number" value="{{ $cartItem['quantity'] }}" min="1"
                                    wire:model.live.debounce.500ms="cart.{{ $cartItem['product_id'] }}.quantity"
                                    class="w-full border-0 bg-transparent text-center focus:ring-0">
                            </div>

                            <div class="w-40 text-right text-sm font-medium text-zinc-600">
                                {{ $cartItem['quantity'] }} x ₼{{ number_format($cartItem['sale_price'], 2) }} =
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
                        <div class="text-center text-red-500 py-10">Səbət boşdur</div>
                    @endforelse
                </div>

                <!-- TOTAL -->
                <div class="border-t pt-4 space-y-2">

                    <!-- MÜŞTƏRİ SEÇİMİ VƏ SÜRƏTLİ ƏLAVƏ ET DÜYMƏSİ -->
                    <div class="space-y-1">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-sm font-medium">Müştəri</span>
                            <div class="relative flex-1">

                                <input type="text" wire:model.live.debounce.300ms="customer_search"
                                    placeholder="Müştəri axtarın..."
                                    class="w-full border-accent-foreground border-0 rounded px-3 py-2 ">

                                @if (count($customerList))

                                    <div
                                        class="absolute left-0 top-full z-50 w-full bg-white border-0 rounded shadow max-h-64 overflow-y-auto">

                                        @foreach ($customerList as $customer)
                                            <div wire:click="selectCustomer({{ $customer->id }})"
                                                class="px-3 py-2 hover:bg-gray-100 border-0 cursor-pointer">

                                                {{ $customer->name }}

                                                @if ($customer->phone)
                                                    ( {{ $customer->phone }} )
                                                @endif

                                                @if ($customer->address)
                                                    - {{ $customer->address }}
                                                @endif

                                            </div>
                                        @endforeach

                                    </div>

                                @endif
                            </div>

                            <flux:button size="sm" variant="subtle" icon="plus" class="text-xs"
                                x-on:click="openModal = true">
                                Yeni Müştəri
                            </flux:button>
                        </div>

                    </div>

                    <hr class="border-zinc-200 my-2" />

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

                    <div class="flex justify-between">
                        <span>Ödəniş növü</span>
                        <flux:select wire:model="payment_method_id" placeholder="Ödəniş növü seçin" class="w-48">
                            @foreach ($this->paymentMethods as $method)
                                <flux:select.option :value="$method->id">{{ $method->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        @error('payment_method_id')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="flex justify-between border-t pt-3 text-xl font-bold">
                        <span>Yekun</span>
                        <span class="font-bold text-red-600 text-2xl"> ₼{{ number_format($this->total, 2) }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span>Qaytarılan</span>
                        <span>₼{{ number_format($this->change, 2) }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span>Qalıq</span>
                        <span>₼{{ number_format($this->deuAmount(), 2) }}</span>
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
                    <h2 class="text-2xl font-bold">Məhsullar</h2>
                    <flux:input autofocus wire:model.live="search" placeholder="Məhsul axtar..." />
                    @if (session()->has('error'))
                        <div
                            class="mt-2 p-4 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded-lg shadow-md">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if (session()->has('success'))
                        <div
                            class="mt-2 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded-lg shadow-md">
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
                                    <div class="font-normal truncate text-base">{{ $product->name }}</div>
                                    <div class="text-xs text-zinc-500">{{ $product->brand?->name }}</div>
                                    <div class="mt-1 text-xs text-zinc-500">
                                        Stok: {{ $product->inventory ? $product->inventory->quantity : 'Yoxdur' }}
                                    </div>
                                    <div class="text-xs text-zinc-500">{{ $product->sku }}</div>
                                    <div class="mt-1 text-xm font-bold">₼ {{ number_format($product->sale_price, 2) }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10 text-red-500 col-span-3">Məhsul tapılmadı</div>
                        @endforelse
                    </div>
                </div>
            </flux:card>

        </div>
    </div>

    <!-- SÜRƏTLİ MÜŞTƏRİ YARATMA MODALI (GRID VƏ CARD-LARIN XARİCİNDƏ, ƏN ALTDA) -->
    <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" x-cloak>
        <div
            class="bg-white dark:bg-zinc-900 p-6 rounded-xl shadow-xl w-full max-w-[450px] space-y-6 border border-zinc-200 dark:border-zinc-800">
            <div>
                <h3 class="text-lg font-bold">Yeni Müştəri Əlavə Et</h3>
                <p class="text-sm text-zinc-500">Səhifədən ayrılmadan müştərini sürətlə qeydiyyata alın.</p>
            </div>

            <div class="space-y-4">
                <flux:input label="Ad və Soyad *" wire:model="new_customer_name" placeholder="Məs. Əli Məmmədov" />
                @error('new_customer_name')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror

                <flux:input label="Telefon Nömrəsi" wire:model="new_customer_phone"
                    placeholder="Məs. +994 50 123 45 67" />
                @error('new_customer_phone')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror

                <flux:input label="Ünvan" wire:model="new_customer_address" placeholder="Məs. Bakı, Nizami küçəsi 10" />
                @error('new_customer_address')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex justify-end gap-2 border-t pt-4">
                <flux:button variant="ghost" x-on:click="openModal = false">Ləğv et</flux:button>
                <flux:button wire:click="quickCreateCustomer" variant="primary">Yadda Saxla</flux:button>
            </div>
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
