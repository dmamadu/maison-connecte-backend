<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Vérifier si la colonne project_id existe déjà
        $hasProjectId = Schema::hasColumn('project_images', 'project_id');
        
        if (!$hasProjectId) {
            // Si la colonne n'existe pas, la créer
            Schema::table('project_images', function (Blueprint $table) {
                $table->foreignId('project_id')
                    ->after('id')
                    ->constrained('projects')
                    ->onDelete('cascade');
            });
        } else {
            // Si elle existe mais n'a pas de contrainte, l'ajouter
            try {
                Schema::table('project_images', function (Blueprint $table) {
                    $table->foreign('project_id')
                        ->references('id')
                        ->on('projects')
                        ->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // La contrainte existe déjà
            }
        }
    }

    public function down(): void
    {
        Schema::table('project_images', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
        });
    }
};