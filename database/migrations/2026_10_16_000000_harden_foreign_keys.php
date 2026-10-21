<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * [cədvəl, sütun, istinad cədvəli, yeni qayda]
     * Əvvəl hamısı cascadeOnDelete idi: bir qeydi silmək tarixçəni də silirdi.
     */
    private array $rules = [
        ['products',              'category_id', 'categories', 'null'],      // kateqoriya silinəndə məhsullar silinmir
        ['products',              'supplier_id', 'suppliers',  'null'],      // təchizatçı silinəndə məhsullar silinmir
        ['purchases',             'supplier_id', 'suppliers',  'restrict'],  // alışı olan təchizatçı silinmir
        ['purchase_items',        'product_id',  'products',   'restrict'],  // alış tarixçəsi olan məhsul silinmir
        ['return_items',          'product_id',  'products',   'restrict'],
        ['damages',               'product_id',  'products',   'restrict'],
        ['damages',               'user_id',     'users',      'restrict'],  // itki qeydləri olan istifadəçi silinmir
        ['customer_transactions', 'customer_id', 'customers',  'restrict'],  // borc tarixçəsi olan müştəri silinmir
    ];

    public function up(): void
    {
        foreach ($this->rules as [$table, $column, $references, $rule]) {
            $this->rewrite($table, $column, $references, $rule);
        }
    }

    public function down(): void
    {
        foreach ($this->rules as [$table, $column, $references]) {
            $this->rewrite($table, $column, $references, 'cascade');
        }
    }

    private function rewrite(string $table, string $column, string $references, string $rule): void
    {
        Schema::table($table, function (Blueprint $t) use ($column) {
            $t->dropForeign([$column]);
        });

        Schema::table($table, function (Blueprint $t) use ($column, $references, $rule) {
            $fk = $t->foreign($column)->references('id')->on($references);

            match ($rule) {
                'null'     => $fk->nullOnDelete(),
                'restrict' => $fk->restrictOnDelete(),
                default    => $fk->cascadeOnDelete(),
            };
        });
    }
};
