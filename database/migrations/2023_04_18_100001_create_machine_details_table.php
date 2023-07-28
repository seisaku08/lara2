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
            $table->string('machine_status')->nullable();
            $table->string('machine_spec')->nullable();
            $table->date('machine_since')->nullable();
            $table->string('machine_os')->nullable();
            $table->string('machine_cpu')->nullable()->comment('CPU');
            $table->string('machine_memory')->nullable()->comment('メモリ');
            $table->string('machine_monitor')->nullable()->comment('モニタ');
            $table->string('machine_powerpoint')->nullable()->comment('PowerPoint');
            $table->boolean('machine_camera')->nullable()->comment('カメラ');
            $table->boolean('machine_hasdrive')->nullable()->comment('DVD/BDドライブ搭載');
            $table->string('machine_connector')->nullable()->comment('接続端子');
            $table->string('machine_canto11')->nullable()->comment('Win11Upgrade');
            $table->string('machine_memo')->nullable()->comment('メモ');
            $table->boolean('machine_is_expired')->comment('破棄');
            $table->timestamps();
            $table->primary('machine_id');

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
