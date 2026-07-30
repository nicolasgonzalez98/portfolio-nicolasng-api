<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Detalle completo para el formulario del admin (incluye ids, flags y orden).
 */
class AdminProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'year' => $this->year,
            'category' => $this->category,
            'status' => $this->status,
            'summary' => $this->summary,
            'description' => $this->description,
            'coverUrl' => $this->cover_url,
            'coverKey' => $this->cover_key,
            'stack' => $this->stack ?? [],
            'features' => $this->features ?? [],
            'techDecisions' => $this->tech_decisions,
            'featured' => (bool) $this->featured,
            'published' => (bool) $this->published,
            'order' => $this->order,
            'links' => $this->links->map(fn ($link) => [
                'id' => $link->id,
                'type' => $link->type,
                'label' => $link->label,
                'url' => $link->url,
            ]),
            'images' => $this->images->map(fn ($image) => [
                'id' => $image->id,
                'url' => $image->url,
                'key' => $image->key,
                'alt' => $image->alt,
            ]),
        ];
    }
}
