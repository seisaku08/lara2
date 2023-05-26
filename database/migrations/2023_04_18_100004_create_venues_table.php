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
        Schema::create('venues', function (Blueprint $table) {
            // $table->id();
            $table->bigIncrements('venue_id');
            $table->string('venue_place')->nullable();
            $table->string('venue_zip');
            $table->string('venue_tel');
            $table->string('venue_addr1');
            $table->string('venue_addr2')->nullable();
            $table->string('venue_addr3')->nullable();
            $table->string('venue_addr4')->nullable();
            $table->string('venue_name');
            $table->timestamps();
            
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};
