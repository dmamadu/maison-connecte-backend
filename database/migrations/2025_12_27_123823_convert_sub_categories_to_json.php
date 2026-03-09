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
        $subCategories = DB::table('sub_categories')->get()->map(function($subCat) {
            return [
                'id' => $subCat->id,
                'category_id' => $subCat->category_id,
                'slug' => $subCat->slug,
                'name' => $subCat->name,
                'created_at' => $subCat->created_at,
                'updated_at' => $subCat->updated_at,
            ];
        })->toArray();

        // 3. Supprimer et recréer la table
        Schema::dropIfExists('sub_categories');
        
        Schema::create('sub_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->json('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // 4. Réinsérer les données converties
        foreach ($subCategories as $subCat) {
            DB::table('sub_categories')->insert([
                'id' => $subCat['id'],
                'category_id' => $subCat['category_id'],
                'slug' => $subCat['slug'],
                'name' => json_encode([
                    'fr' => $subCat['name'],
                    'en' => $subCat['name'],
                ]),
                'created_at' => $subCat['created_at'],
                'updated_at' => $subCat['updated_at'],
            ]);
        }

        // 5. Réactiver les vérifications de clés étrangères
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $subCategories = DB::table('sub_categories')->get()->map(function($subCat) {
            $name = json_decode($subCat->name, true);
            
            return [
                'id' => $subCat->id,
                'category_id' => $subCat->category_id,
                'slug' => $subCat->slug,
                'name' => is_array($name) ? ($name['fr'] ?? '') : $name,
                'created_at' => $subCat->created_at,
                'updated_at' => $subCat->updated_at,
            ];
        })->toArray();

        Schema::dropIfExists('sub_categories');
        
        Schema::create('sub_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        foreach ($subCategories as $subCat) {
            DB::table('sub_categories')->insert($subCat);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};