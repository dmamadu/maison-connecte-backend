// database/migrations/xxxx_create_project_types_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_types', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('name'); // {"fr": "Villa", "en": "Villa"}
            $table->json('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->default('#3B82F6');
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_types');
    }
};