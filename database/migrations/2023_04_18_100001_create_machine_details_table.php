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
            $table->bigInteger('machine_id')->unsigned();
            $table->string('machine_name');
            $table->string('machine_status');
            $table->string('machine_spec');
            $table->string('machine_os');
            $table->string('machine_since');
            // $table->string('machine_camera')->comment('カメラ');
            // $table->string('machine_cpu')->comment('CPU');
            // $table->string('machine_memory')->comment('メモリ');
            // $table->string('machine_hasdrive')->comment('DVD/BDドライブ搭載');
            // $table->string('machine_connector')->comment('接続端子');
            // $table->string('machine_canto11')->comment('Win11Upgrade');
            $table->string('machine_memo');
            $table->boolean('machine_is_expired');
            $table->timestamps();
        });
        Schema::table('machine_details', function (Blueprint $table) {

            $table->bigIncrements('machine_id')->change();


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
