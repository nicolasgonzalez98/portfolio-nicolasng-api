<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'year',
        'category',
        'status',
        'summary',
        'description',
        'cover_url',
        'cover_key',
        'stack',
        'features',
        'tech_decisions',
        'featured',
        'published',
        'order',
    ];

    protected $casts = [
        'stack' => 'array',
        'features' => 'array',
        'tech_decisions' => 'array',
        'featured' => 'boolean',
        'published' => 'boolean',
        'year' => 'integer',
        'order' => 'integer',
    ];

    // Las URLs de detalle usan el slug, no el id.
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function links(): HasMany
    {
        return $this->hasMany(ProjectLink::class)->orderBy('order');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class)->orderBy('order');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('published', true);
    }
}
