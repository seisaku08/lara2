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
        Schema::create('shippings', function (Blueprint $table) {
            // $table->id();
            $table->bigInteger('shipping_id');
            $table->bigInteger('order_id')->unsigned();
            $table->bigInteger('venue_id')->unsigned();
            $table->date('shipping_arrive_day');
            $table->string('shipping_arrive_time');
            $table->date('shipping_return_day');
            $table->timestamps();
            $table->primary(['shipping_id','order_id','venue_id']);
        });

        Schema::table('shippings', function (Blueprint $table) {

            $table->increments('shipping_id')->change();

            // 外部キー制約
            $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('venue_id')->references('venue_id')->on('venues')->onDelete('cascade')->onUpdate('cascade');
            
        });


    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shippings');
    }
};
