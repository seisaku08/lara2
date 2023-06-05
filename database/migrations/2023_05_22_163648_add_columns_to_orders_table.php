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
        Schema::table('orders', function (Blueprint $table) {

            $table->integer('token')->comment('重複対策トークン');
            $table->boolean('seminar_venue_pending')->nullable()->comment('送付先住所後送フラグ');
            $table->boolean('reminder_sent')->nullable()->comment('リマインダメール送付済フラグ');
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->dropColumn('token');
            $table->dropColumn('seminar_venue_pending');
            $table->dropColumn('reminder_sent');
            //
        });
    }
};
