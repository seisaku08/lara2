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
            $table->bigInteger('id');
            $table->date('day');
            $table->bigInteger('machine_id')->unsigned();
            $table->timestamps();
            $table->primary(['id','day','machine_id']);
        });

        Schema::table('day_machine_detail', function (Blueprint $table) {

            $table->increments('id')->change();

            // 外部キー制約
            $table->foreign('day')->references('day')->on('days')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('machine_id')->references('machine_id')->on('machine_details')->onDelete('cascade')->onUpdate('cascade');

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
