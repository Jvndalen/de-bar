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
        Schema::table('treat_balances', function (Blueprint $table) {
            $table->foreign(['user_id'], 'treat_balance_user_id_user_id_fk')->references(['id'])->on('users')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('treat_balance', function (Blueprint $table) {
            $table->dropForeign('treat_balance_user_id_user_id_fk');
        });
    }
};
