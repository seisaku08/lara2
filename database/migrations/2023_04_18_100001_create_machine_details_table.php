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
        Schema::create('machine_details', function (Blueprint $table) {
            //$table->id();
            $table->bigIncrements('machine_id');
            $table->string('machine_name');
            $table->string('machine_status');
            $table->string('machine_spec');
            $table->string('machine_os');
            $table->string('machine_since');
            $table->string('machine_memo');
            $table->boolean('machine_is_expired');
            $table->timestamps();
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machine_details');
    }
};
