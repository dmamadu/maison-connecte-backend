<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class BlogPostController extends Controller
{
    public function index(Request $request)
    {
        return BlogPost::with('category')
            ->when($request->category_id, fn($q, $id) =>
                $q->where('category_id', $id)
            )
            ->orderBy('published_at', 'desc')
            ->get();
    }

    // Option A — route model binding (recommandé, plus simple)
    public function show(BlogPost $post)
    {
        return $post->load('category');
    }
}