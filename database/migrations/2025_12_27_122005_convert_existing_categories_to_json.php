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
        $categories = DB::table('categories')->get()->map(function($cat) {
            return [
                'id' => $cat->id,
                'slug' => $cat->slug,
                'name' => $cat->name,
                'description' => $cat->description,
                'created_at' => $cat->created_at,
                'updated_at' => $cat->updated_at,
            ];
        })->toArray();

        // 3. Supprimer et recréer la table
        Schema::dropIfExists('categories');
        
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('name');
            $table->json('description')->nullable();
            $table->timestamps();
        });

        // 4. Réinsérer les données converties
        foreach ($categories as $cat) {
            DB::table('categories')->insert([
                'id' => $cat['id'],
                'slug' => $cat['slug'],
                'name' => json_encode([
                    'fr' => $cat['name'],
                    'en' => $cat['name'],
                ]),
                'description' => $cat['description'] 
                    ? json_encode([
                        'fr' => $cat['description'],
                        'en' => $cat['description'],
                    ])
                    : null,
                'created_at' => $cat['created_at'],
                'updated_at' => $cat['updated_at'],
            ]);
        }

        // 5. Réactiver les vérifications de clés étrangères
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $categories = DB::table('categories')->get()->map(function($cat) {
            $name = json_decode($cat->name, true);
            $description = $cat->description ? json_decode($cat->description, true) : null;
            
            return [
                'id' => $cat->id,
                'slug' => $cat->slug,
                'name' => is_array($name) ? ($name['fr'] ?? '') : $name,
                'description' => is_array($description) ? ($description['fr'] ?? null) : $description,
                'created_at' => $cat->created_at,
                'updated_at' => $cat->updated_at,
            ];
        })->toArray();

        Schema::dropIfExists('categories');
        
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        foreach ($categories as $cat) {
            DB::table('categories')->insert($cat);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};