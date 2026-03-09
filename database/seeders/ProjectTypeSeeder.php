<?php

namespace Database\Seeders;

use App\Models\ProjectType;
use Illuminate\Database\Seeder;

class ProjectTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'slug' => 'villa',
                'name' => [
                    'fr' => 'Villa',
                    'en' => 'Villa'
                ],
                'description' => [
                    'fr' => 'Projets de villas résidentielles',
                    'en' => 'Residential villa projects'
                ],
                'icon' => 'heroicon-o-home',
                'color' => '#3B82F6',
                'order' => 1
            ],
            [
                'slug' => 'apartment',
                'name' => [
                    'fr' => 'Appartement',
                    'en' => 'Apartment'
                ],
                'description' => [
                    'fr' => 'Projets d\'appartements et résidences',
                    'en' => 'Apartment and residence projects'
                ],
                'icon' => 'heroicon-o-building-office-2',
                'color' => '#10B981',
                'order' => 2
            ],
            [
                'slug' => 'office',
                'name' => [
                    'fr' => 'Bureau',
                    'en' => 'Office'
                ],
                'description' => [
                    'fr' => 'Espaces de bureaux professionnels',
                    'en' => 'Professional office spaces'
                ],
                'icon' => 'heroicon-o-briefcase',
                'color' => '#F59E0B',
                'order' => 3
            ],
            [
                'slug' => 'commercial',
                'name' => [
                    'fr' => 'Commercial',
                    'en' => 'Commercial'
                ],
                'description' => [
                    'fr' => 'Espaces commerciaux et boutiques',
                    'en' => 'Commercial spaces and shops'
                ],
                'icon' => 'heroicon-o-building-storefront',
                'color' => '#EF4444',
                'order' => 4
            ],
        ];

        foreach ($types as $type) {
            ProjectType::create($type);
        }
    }
}
