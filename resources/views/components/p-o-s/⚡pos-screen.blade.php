<?php

use Livewire\Component;

new class extends Component
{
    public $search = '';
     
};
?>
<div>

<div class="h-[calc(100vh-120px)]">
    <div class="grid h-full grid-cols-[1fr_550px] gap-4">

                <!-- CHECKOUT -->
        <flux:card class="flex h-full flex-col">

            <div class="border-b border-zinc-200  pb-4">
                <div class="flex items-center justify-between">

                    <h2 class="text-2xl font-bold">
                        Səbət
                    </h2>

                    <span
                        class="rounded-full bg-primary-100 px-3 py-1 text-xs font-medium">
                        3 məhsul
                    </span>

                </div>
            </div>

            <!-- CART ITEMS -->
            <div class="flex-1 space-y-2 overflow-y-auto py-4">

<div class="flex items-center rounded-lg border border-gray-200 bg-gray-50 p-3">

    <div class="flex-1 font-medium truncate">
        Coca Cola 1L
    </div>

    <div class="mx-4 w-14">
        <input
            type="number"
            value="2"
            min="1"
            class="w-full border-0 bg-transparent text-center focus:ring-0">
    </div>

    <div class="w-40 text-right text-sm font-medium text-zinc-600">
        ₼5.00
    </div>

    <div class="ml-8">
        <flux:button
            size="sm"
            variant="danger">
            ✕
        </flux:button>
    </div>

</div>

            </div>

            <!-- TOTAL -->
            <div class="border-t pt-4 space-y-2">

                <div class="flex justify-between">
                    <span>Ara cəm</span>
                    <span>₼15.00</span>
                </div>

                <div class="flex justify-between text-red-500">
                    <span>Endirim</span>
                    <span>- ₼1.00</span>
                </div>

                <div
                    class="flex justify-between border-t pt-3 text-xl font-bold">

                    <span>Yekun</span>
                    <span>₼14.00</span>

                </div>

                <flux:input
                    placeholder="Ödənilən məbləğ" />

                <flux:button
                    variant="primary"
                    class="w-full">
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

                <flux:input autofocus
                    placeholder="Məhsul axtar..."
                />
            </div>

            <div class="mt-4 flex-1 overflow-y-auto">

                <div class="grid grid-cols-3 gap-4">

                    <div
                        class="cursor-pointer overflow-hidden rounded-xl border bg-white transition hover:shadow-lg">

                        <div class="h-32 bg-zinc-100 flex items-center justify-center">
                            Şəkil
                        </div>

                        <div class="p-3">
                            <div class="font-semibold truncate">
                                Coca Cola 1L
                            </div>

                            <div class="text-xs text-zinc-500">
                                SKU: COLA001
                            </div>

                            <div class="mt-1 text-xs text-zinc-500">
                                Stok: 25
                            </div>

                            <div class="mt-2 text-lg font-bold">
                                ₼2.50
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </flux:card>



    </div>
</div>


</div>