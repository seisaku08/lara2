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
        Schema::create('supplies', function (Blueprint $table) {
            $table->bigInteger('supply_id')->unsigned();
            $table->bigInteger('machine_id')->unsigned();
            $table->string('supply_name')->nullable();
            $table->string('supply_memo')->nullable();
            $table->timestamps();
            $table->primary(['supply_id','machine_id'])->change();

        });

        Schema::table('supplies', function (Blueprint $table) {

            $table->bigIncrements('supply_id')->change();
            $table->foreign('machine_id')->references('machine_id')->on('machine_details')->onDelete('cascade')->onUpdate('cascade');

        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplies');
    }
};
