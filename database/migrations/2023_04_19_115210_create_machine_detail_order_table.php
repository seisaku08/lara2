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
        Schema::create('machine_detail_order', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->bigInteger('machine_id')->unsigned();
            $table->bigInteger('order_id')->unsigned();
            $table->timestamps();
            $table->primary(['id','machine_id','order_id']);
        });
        
        Schema::table('machine_detail_order', function (Blueprint $table) {
            $table->bigIncrements('id')->change();

            // 外部キー制約
            $table->foreign('machine_id')->references('machine_id')->on('machine_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade')->onUpdate('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machine_detail_order');
    }
};
