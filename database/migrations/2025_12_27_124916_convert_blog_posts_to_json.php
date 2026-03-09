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
        $blogPosts = DB::table('blog_posts')->get()->map(function($post) {
            return [
                'id' => $post->id,
                'category_id' => $post->category_id,
                'title' => $post->title,
                'excerpt' => $post->excerpt,
                'image' => $post->image,
                'author' => $post->author,
                'read_time' => $post->read_time,
                'published_at' => $post->published_at,
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at,
            ];
        })->toArray();

        // 3. Supprimer et recréer la table
        Schema::dropIfExists('blog_posts');
        
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->json('title');
            $table->json('excerpt')->nullable();
            $table->string('image')->nullable();
            $table->string('author')->nullable();
            $table->string('read_time')->nullable();
            $table->date('published_at');
            $table->timestamps();
        });

        // 4. Réinsérer les données converties
        foreach ($blogPosts as $post) {
            DB::table('blog_posts')->insert([
                'id' => $post['id'],
                'category_id' => $post['category_id'],
                'title' => json_encode([
                    'fr' => $post['title'],
                    'en' => $post['title'],
                ]),
                'excerpt' => $post['excerpt'] 
                    ? json_encode([
                        'fr' => $post['excerpt'],
                        'en' => $post['excerpt'],
                    ])
                    : null,
                'image' => $post['image'],
                'author' => $post['author'],
                'read_time' => $post['read_time'],
                'published_at' => $post['published_at'],
                'created_at' => $post['created_at'],
                'updated_at' => $post['updated_at'],
            ]);
        }

        // 5. Réactiver les vérifications de clés étrangères
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $blogPosts = DB::table('blog_posts')->get()->map(function($post) {
            $title = json_decode($post->title, true);
            $excerpt = $post->excerpt ? json_decode($post->excerpt, true) : null;
            
            return [
                'id' => $post->id,
                'category_id' => $post->category_id,
                'title' => is_array($title) ? ($title['fr'] ?? '') : $title,
                'excerpt' => is_array($excerpt) ? ($excerpt['fr'] ?? null) : $excerpt,
                'image' => $post->image,
                'author' => $post->author,
                'read_time' => $post->read_time,
                'published_at' => $post->published_at,
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at,
            ];
        })->toArray();

        Schema::dropIfExists('blog_posts');
        
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('excerpt')->nullable();
            $table->string('image')->nullable();
            $table->string('author')->nullable();
            $table->string('read_time')->nullable();
            $table->date('published_at');
            $table->timestamps();
        });

        foreach ($blogPosts as $post) {
            DB::table('blog_posts')->insert($post);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};