<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CoffeeShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Settings
        \App\Models\Setting::create(['key' => 'payment_gateway_active', 'value' => 'false', 'type' => 'boolean']);
        \App\Models\Setting::create(['key' => 'store_name', 'value' => 'Coffee POS', 'type' => 'string']);

        // Ingredients
        $kopi = \App\Models\Ingredient::create(['name' => 'Biji Kopi House Blend', 'unit' => 'gr', 'stock' => 5000, 'min_stock' => 1000, 'cost_per_unit' => 200]); // Rp200/gr
        $susu = \App\Models\Ingredient::create(['name' => 'Susu Fresh Milk', 'unit' => 'ml', 'stock' => 10000, 'min_stock' => 2000, 'cost_per_unit' => 25]); // Rp25/ml
        $gula = \App\Models\Ingredient::create(['name' => 'Gula Cair', 'unit' => 'ml', 'stock' => 5000, 'min_stock' => 1000, 'cost_per_unit' => 10]); // Rp10/ml
        $sirupVanilla = \App\Models\Ingredient::create(['name' => 'Sirup Vanilla', 'unit' => 'ml', 'stock' => 2000, 'min_stock' => 500, 'cost_per_unit' => 50]); // Rp50/ml
        $cup = \App\Models\Ingredient::create(['name' => 'Cup Plastik', 'unit' => 'pcs', 'stock' => 1000, 'min_stock' => 200, 'cost_per_unit' => 1500]); // Rp1500/pcs

        // Categories
        $catCoffee = \App\Models\Category::create(['id' => \Illuminate\Support\Str::uuid(), 'name' => 'Coffee']);
        $catNonCoffee = \App\Models\Category::create(['id' => \Illuminate\Support\Str::uuid(), 'name' => 'Non-Coffee']);

        // Products
        $latte = \App\Models\Product::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'sku' => 'CF-LATTE',
            'name' => 'Cafe Latte',
            'description' => 'Espresso with fresh milk',
            'category_id' => $catCoffee->id,
            'buy_price' => 0,
            'selling_price' => 25000,
            'is_recipe_based' => true,
            'has_customization' => true,
        ]);

        $americano = \App\Models\Product::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'sku' => 'CF-AMER',
            'name' => 'Americano',
            'description' => 'Espresso with water',
            'category_id' => $catCoffee->id,
            'buy_price' => 0,
            'selling_price' => 20000,
            'is_recipe_based' => true,
            'has_customization' => true,
        ]);

        // Recipes - Latte Small
        \App\Models\ProductRecipe::create(['product_id' => $latte->id, 'size' => 'Small', 'temperature' => 'Hot', 'ingredient_id' => $kopi->id, 'quantity' => 14]);
        \App\Models\ProductRecipe::create(['product_id' => $latte->id, 'size' => 'Small', 'temperature' => 'Hot', 'ingredient_id' => $susu->id, 'quantity' => 150]);
        \App\Models\ProductRecipe::create(['product_id' => $latte->id, 'size' => 'Small', 'temperature' => 'Hot', 'ingredient_id' => $cup->id, 'quantity' => 1]);
        
        // Recipes - Latte Medium
        \App\Models\ProductRecipe::create(['product_id' => $latte->id, 'size' => 'Medium', 'temperature' => 'Hot', 'ingredient_id' => $kopi->id, 'quantity' => 18]);
        \App\Models\ProductRecipe::create(['product_id' => $latte->id, 'size' => 'Medium', 'temperature' => 'Hot', 'ingredient_id' => $susu->id, 'quantity' => 200]);
        \App\Models\ProductRecipe::create(['product_id' => $latte->id, 'size' => 'Medium', 'temperature' => 'Hot', 'ingredient_id' => $cup->id, 'quantity' => 1]);

        // Add-ons
        \App\Models\AddOn::create(['name' => 'Extra Shot Espresso', 'price' => 5000]);
        \App\Models\AddOn::create(['name' => 'Oat Milk', 'price' => 8000]);
        \App\Models\AddOn::create(['name' => 'Syrup Vanilla', 'price' => 4000]);
    }
}
