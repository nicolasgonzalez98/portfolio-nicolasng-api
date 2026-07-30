<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Listado del panel admin: incluye id, estado de publicación y orden
 * (a diferencia del listado público, acá se ven también los borradores).
 */
class AdminProjectListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'category' => $this->category,
            'featured' => (bool) $this->featured,
            'published' => (bool) $this->published,
            'order' => $this->order,
        ];
    }
}
