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
        Schema::create('invoices', function (Blueprint $table) {
            $table->bigInteger('id')->unsigned();
            $table->Integer('shipping_id')->unsigned();
            $table->string('invoice_no');
            $table->timestamps();

            $table->primary(['id','shipping_id']);
        });

        Schema::table('invoices', function (Blueprint $table) {

            $table->increments('id')->change();

            // 外部キー制約
            $table->foreign('shipping_id')->references('shipping_id')->on('shippings')->onDelete('cascade')->onUpdate('cascade');
            
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
