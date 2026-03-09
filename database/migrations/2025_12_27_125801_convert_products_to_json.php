<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Désactiver temporairement les vérifications de clés étrangères
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 2. Sauvegarder les données
        $products = DB::table('products')->get()->map(function($product) {
            return [
                'id' => $product->id,
                'category_id' => $product->category_id,
                'subcategory_id' => $product->subcategory_id,
                'title' => $product->title,
                'description' => $product->description,
                'price' => $product->price,
                'image' => $product->image,
                'link' => $product->link,
                'highlights' => $product->highlights,
                'specs' => $product->specs,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ];
        })->toArray();

        // 3. Supprimer et recréer la table
        Schema::dropIfExists('products');
        
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('subcategory_id')->constrained('sub_categories')->cascadeOnDelete();
            $table->json('title');
            $table->json('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('image')->nullable();
            $table->string('link')->nullable();
            $table->json('highlights')->nullable();
            $table->json('specs')->nullable();
            $table->timestamps();
        });

        // 4. Réinsérer les données converties
        foreach ($products as $product) {
            // Gérer highlights et specs (déjà JSON ou non)
            $highlights = $product['highlights'];
            if ($highlights && !is_array(json_decode($highlights, true))) {
                $highlights = json_encode([$highlights]);
            }
            
            $specs = $product['specs'];
            if ($specs && !is_array(json_decode($specs, true))) {
                $specs = json_encode([$specs]);
            }

            DB::table('products')->insert([
                'id' => $product['id'],
                'category_id' => $product['category_id'],
                'subcategory_id' => $product['subcategory_id'],
                'title' => json_encode([
                    'fr' => $product['title'],
                    'en' => $product['title'],
                ]),
                'description' => $product['description'] 
                    ? json_encode([
                        'fr' => $product['description'],
                        'en' => $product['description'],
                    ])
                    : null,
                'price' => $product['price'],
                'image' => $product['image'],
                'link' => $product['link'],
                'highlights' => $highlights,
                'specs' => $specs,
                'created_at' => $product['created_at'],
                'updated_at' => $product['updated_at'],
            ]);
        }

        // 5. Réactiver les vérifications de clés étrangères
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $products = DB::table('products')->get()->map(function($product) {
            $title = json_decode($product->title, true);
            $description = $product->description ? json_decode($product->description, true) : null;
            
            return [
                'id' => $product->id,
                'category_id' => $product->category_id,
                'subcategory_id' => $product->subcategory_id,
                'title' => is_array($title) ? ($title['fr'] ?? '') : $title,
                'description' => is_array($description) ? ($description['fr'] ?? null) : $description,
                'price' => $product->price,
                'image' => $product->image,
                'link' => $product->link,
                'highlights' => $product->highlights,
                'specs' => $product->specs,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ];
        })->toArray();

        Schema::dropIfExists('products');
        
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('subcategory_id')->constrained('sub_categories')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->float('price');
            $table->string('image');
            $table->string('link')->nullable();
            $table->json('highlights')->nullable();
            $table->json('specs')->nullable();
            $table->timestamps();
        });

        foreach ($products as $product) {
            DB::table('products')->insert($product);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};