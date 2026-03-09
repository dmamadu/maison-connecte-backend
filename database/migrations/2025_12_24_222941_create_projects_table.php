// database/migrations/xxxx_create_projects_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_type_id')->constrained()->onDelete('cascade');
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
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};