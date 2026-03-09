<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Désactiver les clés étrangères
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Supprimer les anciennes tables
        Schema::dropIfExists('project_services');
        Schema::dropIfExists('project_tags');
        Schema::dropIfExists('project_images');
        
        // Recréer project_images
        Schema::create('project_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('image_path');
            $table->integer('order')->default(0);
            $table->timestamps();
        });
        
        // Recréer project_tags
        Schema::create('project_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('tag');
            $table->timestamps();
        });
        
        // Recréer project_services
        Schema::create('project_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->enum('service', ['security', 'automation', 'solar', 'finishing']);
            $table->timestamps();
        });
        
        // Réactiver les clés étrangères
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        Schema::dropIfExists('project_services');
        Schema::dropIfExists('project_tags');
        Schema::dropIfExists('project_images');
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};