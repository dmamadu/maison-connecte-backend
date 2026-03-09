<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Liste de tous les clients
     */
    public function index(Request $request)
    {
        $query = Client::query();

        // Filtrer seulement les actifs
        if ($request->boolean('active_only', true)) {
            $query->active();
        }

        // Filtrer les clients mis en avant
        if ($request->boolean('featured_only')) {
            $query->featured();
        }

        // Filtrer par secteur
        if ($request->has('industry')) {
            $query->where('industry', $request->industry);
        }

        // Filtrer par ville
        if ($request->has('city')) {
            $query->where('city', 'LIKE', '%' . $request->city . '%');
        }

        // Recherche par nom
        if ($request->has('search')) {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }

        // Trier
        $sortBy = $request->get('sort_by', 'order');
        $sortOrder = $request->get('sort_order', 'asc');
        
        if ($sortBy === 'order') {
            $query->orderByPosition();
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination optionnelle
        if ($request->has('per_page')) {
            $perPage = min($request->per_page, 50);
            return response()->json($query->paginate($perPage));
        }

        $clients = $query->get();

        return response()->json([
            'data' => $clients->map(function($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'slug' => $client->slug,
                    'logo' => $client->logo ? url('storage/' . $client->logo) : null,
                    'website' => $client->website,
                    'industry' => $client->industry,
                    'description' => $client->description,
                    'contact_person' => $client->contact_person,
                    'email' => $client->email,
                    'phone' => $client->phone,
                    'address' => $client->address,
                    'city' => $client->city,
                    'country' => $client->country,
                    'partnership_start' => $client->partnership_start?->format('Y-m-d'),
                    'is_featured' => $client->is_featured,
                    'is_active' => $client->is_active,
                    'order' => $client->order,
                    'created_at' => $client->created_at,
                    'updated_at' => $client->updated_at,
                ];
            })
        ]);
    }

    /**
     * Afficher un client spécifique
     */
    public function show($slug)
    {
        $client = Client::where('slug', $slug)->firstOrFail();

        return response()->json([
            'data' => [
                'id' => $client->id,
                'name' => $client->name,
                'slug' => $client->slug,
                'logo' => $client->logo ? url('storage/' . $client->logo) : null,
                'website' => $client->website,
                'industry' => $client->industry,
                'description' => $client->description,
                'contact_person' => $client->contact_person,
                'email' => $client->email,
                'phone' => $client->phone,
                'address' => $client->address,
                'city' => $client->city,
                'country' => $client->country,
                'partnership_start' => $client->partnership_start?->format('Y-m-d'),
                'is_featured' => $client->is_featured,
                'is_active' => $client->is_active,
                'order' => $client->order,
                'created_at' => $client->created_at,
                'updated_at' => $client->updated_at,
            ]
        ]);
    }

    /**
     * Clients mis en avant
     */
    public function featured(Request $request)
    {
        $limit = $request->get('limit', 10);
        
        $clients = Client::featured()
            ->orderByPosition()
            ->limit($limit)
            ->get();

        return response()->json([
            'data' => $clients->map(function($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'slug' => $client->slug,
                    'logo' => $client->logo ? url('storage/' . $client->logo) : null,
                    'website' => $client->website,
                    'industry' => $client->industry,
                    'is_featured' => $client->is_featured,
                    'order' => $client->order,
                ];
            })
        ]);
    }

    /**
     * Clients par secteur d'activité
     */
    public function byIndustry($industry)
    {
        $clients = Client::active()
            ->where('industry', $industry)
            ->orderByPosition()
            ->get();

        return response()->json([
            'data' => $clients->map(function($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'slug' => $client->slug,
                    'logo' => $client->logo ? url('storage/' . $client->logo) : null,
                    'website' => $client->website,
                    'industry' => $client->industry,
                    'city' => $client->city,
                ];
            })
        ]);
    }

    /**
     * Statistiques des clients
     */
    public function statistics()
    {
        $stats = [
            'total' => Client::active()->count(),
            'featured' => Client::featured()->count(),
            'by_industry' => Client::active()
                ->selectRaw('industry, COUNT(*) as count')
                ->whereNotNull('industry')
                ->groupBy('industry')
                ->orderBy('count', 'desc')
                ->get()
                ->mapWithKeys(function($item) {
                    return [$item->industry => $item->count];
                }),
            'by_city' => Client::active()
                ->selectRaw('city, COUNT(*) as count')
                ->whereNotNull('city')
                ->groupBy('city')
                ->orderBy('count', 'desc')
                ->get()
                ->mapWithKeys(function($item) {
                    return [$item->city => $item->count];
                }),
            'recent' => Client::active()
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function($client) {
                    return [
                        'id' => $client->id,
                        'name' => $client->name,
                        'logo' => $client->logo ? url('storage/' . $client->logo) : null,
                        'created_at' => $client->created_at->format('Y-m-d'),
                    ];
                }),
            'partnership_years' => Client::active()
                ->whereNotNull('partnership_start')
                ->selectRaw('YEAR(partnership_start) as year, COUNT(*) as count')
                ->groupBy('year')
                ->orderBy('year', 'desc')
                ->get()
                ->mapWithKeys(function($item) {
                    return [$item->year => $item->count];
                }),
        ];

        return response()->json($stats);
    }

    /**
     * Liste des secteurs d'activité disponibles
     */
    public function industries()
    {
        $industries = [
            'Technology' => 'Technologie',
            'Construction' => 'Construction',
            'Real Estate' => 'Immobilier',
            'Hospitality' => 'Hôtellerie',
            'Healthcare' => 'Santé',
            'Education' => 'Éducation',
            'Finance' => 'Finance',
            'Retail' => 'Commerce',
            'Manufacturing' => 'Industrie',
            'Energy' => 'Énergie',
            'Other' => 'Autre',
        ];

        return response()->json([
            'data' => collect($industries)->map(function($label, $value) {
                return [
                    'value' => $value,
                    'label' => $label,
                    'count' => Client::active()->where('industry', $value)->count(),
                ];
            })->values()
        ]);
    }

    /**
     * Recherche de clients
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json(['data' => []]);
        }

        $clients = Client::active()
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', '%' . $query . '%')
                  ->orWhere('description', 'LIKE', '%' . $query . '%')
                  ->orWhere('industry', 'LIKE', '%' . $query . '%')
                  ->orWhere('city', 'LIKE', '%' . $query . '%');
            })
            ->orderByPosition()
            ->limit(20)
            ->get();

        return response()->json([
            'data' => $clients->map(function($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'slug' => $client->slug,
                    'logo' => $client->logo ? url('storage/' . $client->logo) : null,
                    'industry' => $client->industry,
                    'city' => $client->city,
                ];
            })
        ]);
    }

    /**
     * Logos uniquement (pour carousel)
     */
    public function logos(Request $request)
    {
        $limit = $request->get('limit', 20);
        
        $clients = Client::active()
            ->orderByPosition()
            ->limit($limit)
            ->get(['id', 'name', 'logo', 'website']);

        return response()->json([
            'data' => $clients->map(function($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'logo' => $client->logo ? url('storage/' . $client->logo) : null,
                    'website' => $client->website,
                ];
            })
        ]);
    }
}