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
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_recipe_based')->default(true);
            $table->boolean('has_customization')->default(true);
            // In sqlite, dropping columns can be tricky, but Laravel supports it well enough now or we just leave them nullable.
            // Let's just make stock and min_stock nullable since they might be used for snacks
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->json('split_payment')->nullable();
            $table->boolean('is_synced')->default(true); // false if offline
        });

        Schema::table('transaction_items', function (Blueprint $table) {
            $table->json('customizations')->nullable();
            $table->string('status')->default('ready'); // pending, on_progress, ready
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->integer('loyalty_points')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_recipe_based', 'has_customization']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['split_payment', 'is_synced']);
        });

        Schema::table('transaction_items', function (Blueprint $table) {
            $table->dropColumn(['customizations', 'status']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('loyalty_points');
        });
    }
};
