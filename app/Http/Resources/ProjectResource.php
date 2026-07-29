<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Versión completa para la página de detalle (/works/{slug}).
 */
class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'title' => $this->title,
            'year' => $this->year,
            'category' => $this->category,
            'status' => $this->status,
            'summary' => $this->summary,
            'description' => $this->description,
            'coverUrl' => $this->cover_url,
            'stack' => $this->stack,
            'features' => $this->features,
            'techDecisions' => $this->tech_decisions,
            'featured' => (bool) $this->featured,
            'links' => $this->links->map(fn ($link) => [
                'type' => $link->type,
                'label' => $link->label,
                'url' => $link->url,
            ]),
            'images' => $this->images->map(fn ($image) => [
                'url' => $image->url,
                'alt' => $image->alt,
            ]),
        ];
    }
}
