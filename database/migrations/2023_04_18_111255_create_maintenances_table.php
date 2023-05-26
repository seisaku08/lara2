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
        Schema::create('maintenances', function (Blueprint $table) {
            //$table->id();
            $table->bigInteger('maintenance_id');
            $table->bigInteger('machine_id')->unsigned();
            $table->date('maintenance_day');
            $table->string('maintenance_detail');
            $table->timestamps();
            $table->primary(['maintenance_id','machine_id']);

        });

        Schema::table('maintenances', function (Blueprint $table) {

            $table->increments('maintenance_id')->change();
            
            // 外部キー制約
            $table->foreign('machine_id')->references('machine_id')->on('machine_details')->onDelete('cascade')->onUpdate('cascade');

        });


    }
    
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
