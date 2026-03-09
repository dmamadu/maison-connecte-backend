<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectService extends Model
{
    protected $fillable = [
        'project_id',
        'service'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}