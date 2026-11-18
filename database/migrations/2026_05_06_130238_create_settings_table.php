<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            // General Settings
            $table->string('logo')->nullable();
            $table->string('app_name')->nullable();
            $table->string('name_store')->nullable();
            $table->string('vat')->nullable();
            $table->string('support_email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->text('address')->nullable();

            // Receipt Settings
            $table->text('receipt_header')->nullable();                 //Çekin yuxarı hissəsindəki mətn
            $table->text('receipt_footer')->nullable();                //Çekin aşağı hissəsindəki mətn
            $table->boolean('receipt_show_logo')->default(true);
            $table->boolean('receipt_show_phone')->default(true);
            $table->boolean('receipt_show_address')->default(true);
            $table->boolean('receipt_show_vat')->default(true);

            // Currency & Tax Settings
            $table->string('currency_code', 3)->nullable();         //AZN, USD, EUR
            $table->string('currency_symbol', 5)->nullable();        // ₼, $, €
            $table->boolean('tax_enabled')->nullable();           //  Vergi faizi
            $table->decimal('tax_rate', 5, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
