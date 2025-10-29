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
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreign(['product_id'], 'transaction_product_id_product_id_fk')->references(['id'])->on('products')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['treat_balance_id'], 'transaction_treat_balance_id_treat_balance_id_fk')->references(['id'])->on('treat_balances')->onUpdate('no action')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction', function (Blueprint $table) {
            $table->dropForeign('transaction_product_id_product_id_fk');
            $table->dropForeign('transaction_treat_balance_id_treat_balance_id_fk');
            $table->dropForeign('transaction_user_id_user_id_fk');
        });
    }
};
