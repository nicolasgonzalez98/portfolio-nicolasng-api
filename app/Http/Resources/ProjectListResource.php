<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Versión liviana para el listado (/works): solo lo que necesita la grilla.
 */
class ProjectListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'title' => $this->title,
            'year' => $this->year,
            'category' => $this->category,
            'summary' => $this->summary,
            'coverUrl' => $this->cover_url,
            'featured' => (bool) $this->featured,
        ];
    }
}
