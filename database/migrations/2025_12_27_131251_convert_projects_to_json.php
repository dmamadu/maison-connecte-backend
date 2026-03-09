<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Sauvegarder les données
        $projects = DB::table('projects')->get()->map(function($project) {
            return [
                'id' => $project->id,
                'project_type_id' => $project->project_type_id ?? null,
                'title' => $project->title ?? '',
                'description' => $project->description ?? '',
                'location' => $project->location ?? '',
                'year' => $project->year ?? '',
                'thumbnail' => $project->thumbnail ?? '',
                'client' => $project->client ?? null,
                'duration' => $project->duration ?? null,
                'is_active' => $project->is_active ?? true,
                'order' => $project->order ?? 0,
                'created_at' => $project->created_at,
                'updated_at' => $project->updated_at,
            ];
        })->toArray();

        // Supprimer et recréer
        Schema::dropIfExists('projects');
        
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_type_id')->constrained('project_types')->onDelete('cascade');
            $table->json('title');
            $table->json('description');
            $table->string('location');
            $table->string('year');
            $table->string('thumbnail');
            $table->string('client')->nullable();
            $table->string('duration')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Réinsérer les données
        foreach ($projects as $project) {
            if ($project['project_type_id']) {
                DB::table('projects')->insert([
                    'id' => $project['id'],
                    'project_type_id' => $project['project_type_id'],
                    'title' => json_encode([
                        'fr' => $project['title'],
                        'en' => $project['title'],
                    ]),
                    'description' => json_encode([
                        'fr' => $project['description'],
                        'en' => $project['description'],
                    ]),
                    'location' => $project['location'],
                    'year' => $project['year'],
                    'thumbnail' => $project['thumbnail'],
                    'client' => $project['client'],
                    'duration' => $project['duration'],
                    'is_active' => $project['is_active'],
                    'order' => $project['order'],
                    'created_at' => $project['created_at'],
                    'updated_at' => $project['updated_at'],
                ]);
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $projects = DB::table('projects')->get()->map(function($project) {
            $title = json_decode($project->title, true);
            $description = json_decode($project->description, true);
            
            return [
                'id' => $project->id,
                'project_type_id' => $project->project_type_id,
                'title' => is_array($title) ? ($title['fr'] ?? '') : $title,
                'description' => is_array($description) ? ($description['fr'] ?? '') : $description,
                'location' => $project->location,
                'year' => $project->year,
                'thumbnail' => $project->thumbnail,
                'client' => $project->client,
                'duration' => $project->duration,
                'is_active' => $project->is_active,
                'order' => $project->order,
                'created_at' => $project->created_at,
                'updated_at' => $project->updated_at,
            ];
        })->toArray();

        Schema::dropIfExists('projects');
        
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_type_id')->constrained('project_types')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('location');
            $table->string('year');
            $table->string('thumbnail');
            $table->string('client')->nullable();
            $table->string('duration')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        foreach ($projects as $project) {
            DB::table('projects')->insert($project);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};