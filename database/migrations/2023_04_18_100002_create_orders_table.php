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
        Schema::create('orders', function (Blueprint $table) {
            // $table->id();
            $table->bigInteger('order_id')->unsigned();
            $table->string('order_no')->nullable();
            $table->bigInteger('user_id')->unsigned();
            $table->date('seminar_day')->nullable();
            $table->string('seminar_name')->nullable();
            $table->string('order_status')->comment('現在の状況');
            $table->date('order_use_from')->nullable();
            $table->date('order_use_to')->nullable();
            $table->integer('token')->comment('重複対策トークン');
            $table->boolean('seminar_venue_pending')->nullable()->comment('送付先住所後送フラグ');
            $table->boolean('reminder_sent')->nullable()->comment('リマインダメール送付済フラグ');
            $table->datetime('nine_day_before')->nullable()->comment('リマインダメール送付日「9営業日前」');
            $table->string('temporary_name')->nullable()->comment('仮担当者');
            $table->timestamps();
            $table->primary(['order_id','user_id','order_status'])->change();

        });

        Schema::table('orders', function (Blueprint $table) {
            $table->bigIncrements('order_id')->change();


            // 外部キー制約
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
