<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProjectType;
use App\Http\Resources\ProjectTypeResource;
use Illuminate\Http\Request;

class ProjectTypeController extends Controller
{
    /**
     * Liste de tous les types de projet
     */
    public function index(Request $request)
    {
        $query = ProjectType::query();

        // Filtrer seulement les actifs si demandé
        if ($request->boolean('active_only')) {
            $query->active();
        }

        // Inclure le compteur de projets si demandé
        if ($request->boolean('include_count')) {
            $query->withCount('activeProjects');
        }

        $projectTypes = $query->orderBy('order')->get();

        return ProjectTypeResource::collection($projectTypes);
    }

    /**
     * Afficher un type de projet spécifique
     */
    public function show($slug)
    {
        $projectType = ProjectType::where('slug', $slug)
            ->with('activeProjects')
            ->firstOrFail();

        return new ProjectTypeResource($projectType);
    }

    /**
     * Obtenir tous les types de projet avec leurs projets
     */
    public function withProjects(Request $request)
    {
        $query = ProjectType::with(['projects' => function($q) {
            $q->where('is_active', true)
              ->orderBy('order')
              ->orderBy('created_at', 'desc');
        }]);

        if ($request->boolean('active_only')) {
            $query->active();
        }

        $projectTypes = $query->orderBy('order')->get();

        return ProjectTypeResource::collection($projectTypes);
    }
}