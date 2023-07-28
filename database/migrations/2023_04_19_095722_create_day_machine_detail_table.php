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
        Schema::create('day_machine_detail', function (Blueprint $table) {
            $table->bigInteger('id')->unsigned();
            $table->date('day');
            $table->bigInteger('machine_id')->unsigned();
            $table->bigInteger('order_id')->unsigned();
            $table->string('order_status')->comment('オーダーの状況');
            $table->timestamps();
            $table->primary(['id','day','machine_id','order_id']);
        });

        Schema::table('day_machine_detail', function (Blueprint $table) {

            $table->bigIncrements('id')->change();

            $table->foreign('machine_id')->references('machine_id')->on('machine_details')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('day_machine_detail');
    }
};
