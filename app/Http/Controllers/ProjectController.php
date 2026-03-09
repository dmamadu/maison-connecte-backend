<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectType;
use App\Http\Resources\ProjectResource;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Liste de tous les projets
     */
    public function index(Request $request)
    {
        $query = Project::with(['projectType', 'images', 'tags', 'services']);

        // Filtrer par type de projet
        if ($request->has('type')) {
            $query->byType($request->type);
        }

        // Filtrer par année
        if ($request->has('year')) {
            $query->where('year', $request->year);
        }

        // Filtrer par localisation
        if ($request->has('location')) {
            $query->where('location', 'LIKE', '%' . $request->location . '%');
        }

        // Filtrer seulement les actifs
        if ($request->boolean('active_only', true)) {
            $query->active();
        }

        // Trier
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if ($sortBy === 'order') {
            $query->orderBy('order', $sortOrder);
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination optionnelle
        if ($request->has('per_page')) {
            $perPage = min($request->per_page, 50); // Max 50 par page
            $projects = $query->paginate($perPage);
            return ProjectResource::collection($projects);
        }

        $projects = $query->get();
        return ProjectResource::collection($projects);
    }

    /**
     * Afficher un projet spécifique
     */
    public function show($id)
    {
        $project = Project::with(['projectType', 'images', 'tags', 'services'])
            ->findOrFail($id);

        return new ProjectResource($project);
    }

    /**
     * Projets par type de projet
     */
    public function byType($typeSlug)
    {
        $projectType = ProjectType::where('slug', $typeSlug)->firstOrFail();

        $projects = Project::with(['projectType', 'images', 'tags', 'services'])
            ->where('project_type_id', $projectType->id)
            ->active()
            ->orderBy('order')
            ->orderBy('created_at', 'desc')
            ->get();

        return ProjectResource::collection($projects);
    }

    /**
     * Projets récents/mis en avant
     */
    public function featured(Request $request)
    {
        $limit = $request->get('limit', 6);

        $projects = Project::with(['projectType', 'images', 'tags', 'services'])
            ->active()
            ->orderBy('order')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return ProjectResource::collection($projects);
    }

    /**
     * Projets par service
     */
    public function byService($service)
    {
        $validServices = ['security', 'automation', 'solar', 'finishing'];
        
        if (!in_array($service, $validServices)) {
            return response()->json(['message' => 'Service invalide'], 400);
        }

        $projects = Project::with(['projectType', 'images', 'tags', 'services'])
            ->whereHas('services', function($query) use ($service) {
                $query->where('service', $service);
            })
            ->active()
            ->orderBy('created_at', 'desc')
            ->get();

        return ProjectResource::collection($projects);
    }

    /**
     * Projets par tag
     */
    public function byTag($tag)
    {
        $projects = Project::with(['projectType', 'images', 'tags', 'services'])
            ->whereHas('tags', function($query) use ($tag) {
                $query->where('tag', 'LIKE', '%' . $tag . '%');
            })
            ->active()
            ->orderBy('created_at', 'desc')
            ->get();

        return ProjectResource::collection($projects);
    }

    /**
     * Projets par année
     */
    public function byYear($year)
    {
        $projects = Project::with(['projectType', 'images', 'tags', 'services'])
            ->where('year', $year)
            ->active()
            ->orderBy('created_at', 'desc')
            ->get();

        return ProjectResource::collection($projects);
    }

    /**
     * Projets par localisation
     */
    public function byLocation(Request $request)
    {
        $location = $request->get('location');

        if (!$location) {
            return response()->json(['message' => 'Localisation requise'], 400);
        }

        $projects = Project::with(['projectType', 'images', 'tags', 'services'])
            ->where('location', 'LIKE', '%' . $location . '%')
            ->active()
            ->orderBy('created_at', 'desc')
            ->get();

        return ProjectResource::collection($projects);
    }

    /**
     * Statistiques des projets
     */
    public function statistics()
    {
        $stats = [
            'total_projects' => Project::active()->count(),
            'by_type' => ProjectType::withCount(['projects' => function($q) {
                $q->where('is_active', true);
            }])->get()->map(function($type) {
                return [
                    'type' => $type->name,
                    'slug' => $type->slug,
                    'count' => $type->projects_count,
                ];
            }),
            'by_year' => Project::active()
                ->selectRaw('year, COUNT(*) as count')
                ->groupBy('year')
                ->orderBy('year', 'desc')
                ->get(),
            'by_service' => [
                'security' => Project::active()->whereHas('services', fn($q) => $q->where('service', 'security'))->count(),
                'automation' => Project::active()->whereHas('services', fn($q) => $q->where('service', 'automation'))->count(),
                'solar' => Project::active()->whereHas('services', fn($q) => $q->where('service', 'solar'))->count(),
                'finishing' => Project::active()->whereHas('services', fn($q) => $q->where('service', 'finishing'))->count(),
            ],
        ];

        return response()->json($stats);
    }
}