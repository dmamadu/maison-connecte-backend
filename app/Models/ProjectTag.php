<?php

namespace App\Models;

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectTag extends Model
{
    protected $fillable = [
        'project_id',
        'tag'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
