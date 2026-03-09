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
        $projectTypes = DB::table('project_types')->get()->map(function($type) {
            return [
                'id' => $type->id,
                'slug' => $type->slug,
                'name' => $type->name,
                'description' => $type->description,
                'icon' => $type->icon,
                'color' => $type->color,
                'is_active' => $type->is_active,
                'order' => $type->order,
                'created_at' => $type->created_at,
                'updated_at' => $type->updated_at,
            ];
        })->toArray();

        // 3. Supprimer et recréer la table
        Schema::dropIfExists('project_types');
        
        Schema::create('project_types', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('name');
            $table->json('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->default('#3B82F6');
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // 4. Réinsérer les données converties
        foreach ($projectTypes as $type) {
            DB::table('project_types')->insert([
                'id' => $type['id'],
                'slug' => $type['slug'],
                'name' => json_encode([
                    'fr' => $type['name'],
                    'en' => $type['name'],
                ]),
                'description' => $type['description'] 
                    ? json_encode([
                        'fr' => $type['description'],
                        'en' => $type['description'],
                    ])
                    : null,
                'icon' => $type['icon'],
                'color' => $type['color'],
                'is_active' => $type['is_active'],
                'order' => $type['order'],
                'created_at' => $type['created_at'],
                'updated_at' => $type['updated_at'],
            ]);
        }

        // 5. Réactiver les vérifications de clés étrangères
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $projectTypes = DB::table('project_types')->get()->map(function($type) {
            $name = json_decode($type->name, true);
            $description = $type->description ? json_decode($type->description, true) : null;
            
            return [
                'id' => $type->id,
                'slug' => $type->slug,
                'name' => is_array($name) ? ($name['fr'] ?? '') : $name,
                'description' => is_array($description) ? ($description['fr'] ?? null) : $description,
                'icon' => $type->icon,
                'color' => $type->color,
                'is_active' => $type->is_active,
                'order' => $type->order,
                'created_at' => $type->created_at,
                'updated_at' => $type->updated_at,
            ];
        })->toArray();

        Schema::dropIfExists('project_types');
        
        Schema::create('project_types', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->default('#3B82F6');
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        foreach ($projectTypes as $type) {
            DB::table('project_types')->insert($type);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};