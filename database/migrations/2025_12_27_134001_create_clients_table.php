<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo'); // Chemin du logo
            $table->string('website')->nullable();
            $table->string('industry')->nullable(); // Secteur d'activité
            $table->text('description')->nullable();
            $table->string('contact_person')->nullable(); // Personne de contact
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->default('Sénégal');
            $table->date('partnership_start')->nullable(); // Date de début de partenariat
            $table->boolean('is_featured')->default(false); // Client mis en avant
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0); // Ordre d'affichage
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};