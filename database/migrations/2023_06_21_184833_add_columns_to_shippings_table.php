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
        Schema::table('shippings', function (Blueprint $table) {
            $table->boolean('shipping_special')->comment('特記事項あり')->nullable();
            $table->string('shipping_note')->comment('備考')->nullable();

            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shippings', function (Blueprint $table) {
            $table->dropColumn('shipping_special');
            $table->dropColumn('shipping_note');

            //
        });
    }
};
