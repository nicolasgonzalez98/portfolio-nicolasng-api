<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectImageResource;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Services\MediaStorage;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function __construct(private MediaStorage $media) {}

    /**
     * Sube una imagen a la galería de un proyecto.
     */
    public function store(Request $request, Project $project)
    {
        $request->validate([
            'image' => ['required', 'image', 'max:4096'], // 4 MB
            'alt' => ['nullable', 'string', 'max:255'],
        ]);

        $stored = $this->media->store($request->file('image'), "projects/{$project->id}");

        $image = $project->images()->create([
            'url' => $stored['url'],
            'key' => $stored['key'],
            'alt' => $request->input('alt'),
            'order' => ((int) $project->images()->max('order')) + 1,
        ]);

        return (new ProjectImageResource($image))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Reemplaza la portada del proyecto (borra el objeto anterior si existía).
     */
    public function cover(Request $request, Project $project)
    {
        $request->validate([
            'image' => ['required', 'image', 'max:4096'],
        ]);

        $this->media->delete($project->cover_key);

        $stored = $this->media->store($request->file('image'), "projects/{$project->id}/cover");

        $project->update([
            'cover_url' => $stored['url'],
            'cover_key' => $stored['key'],
        ]);

        return response()->json(['coverUrl' => $project->cover_url]);
    }

    /**
     * Borra una imagen (objeto del disco + fila).
     */
    public function destroy(ProjectImage $image)
    {
        $this->media->delete($image->key);
        $image->delete();

        return response()->json(['message' => 'Imagen eliminada.']);
    }
}
