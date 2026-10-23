<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hər məhsulun yalnız BİR anbar sətri ola bilər.
     * Əvvəl təkrarlanan sətirlər varsa, miqdarları toplayıb ən köhnə sətirdə birləşdirir.
     * (Adətən təkrar: avtomatik yaranan 0 sətri + əllə yazılan miqdar sətri, cəm doğrudur.)
     * İşlətməzdən əvvəl təkrarlara baxın:
     *   SELECT product_id, COUNT(*) c, GROUP_CONCAT(id) ids, GROUP_CONCAT(quantity) qtys
     *   FROM inventories GROUP BY product_id HAVING c > 1;
     */
    public function up(): void
    {
        $duplicated = DB::table('inventories')
            ->select('product_id')
            ->groupBy('product_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('product_id');

        foreach ($duplicated as $productId) {
            $rows = DB::table('inventories')
                ->where('product_id', $productId)
                ->orderBy('id')
                ->get();

            $keep = $rows->first();

            DB::table('inventories')
                ->where('id', $keep->id)
                ->update(['quantity' => round((float) $rows->sum('quantity'), 2)]);

            DB::table('inventories')
                ->where('product_id', $productId)
                ->where('id', '!=', $keep->id)
                ->delete();
        }

        Schema::table('inventories', function (Blueprint $table) {
            $table->unique('product_id');
        });
    }

    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropUnique(['product_id']);
        });
    }
};
