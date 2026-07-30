<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectRequest;
use App\Http\Resources\AdminProjectListResource;
use App\Http\Resources\AdminProjectResource;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    /**
     * Todos los proyectos (publicados y borradores).
     */
    public function index()
    {
        return AdminProjectListResource::collection(
            Project::orderBy('order')->get()
        );
    }

    public function show(Project $project)
    {
        $project->load(['links', 'images']);

        return new AdminProjectResource($project);
    }

    public function store(ProjectRequest $request)
    {
        $project = DB::transaction(function () use ($request) {
            $project = Project::create($this->mapData($request));
            $this->syncLinks($project, $request->input('links', []));

            return $project;
        });

        $project->load(['links', 'images']);

        return (new AdminProjectResource($project))
            ->response()
            ->setStatusCode(201);
    }

    public function update(ProjectRequest $request, Project $project)
    {
        DB::transaction(function () use ($request, $project) {
            $project->update($this->mapData($request));
            $this->syncLinks($project, $request->input('links', []));
        });

        $project->load(['links', 'images']);

        return new AdminProjectResource($project);
    }

    public function destroy(Project $project)
    {
        // En 3c: borrar también los objetos de R2 antes de esto.
        // El borrado en cascada (FK) elimina links e images en la DB.
        $project->delete();

        return response()->json(['message' => 'Proyecto eliminado.']);
    }

    /**
     * Mapea el payload (camelCase) a las columnas del modelo (snake_case).
     */
    private function mapData(ProjectRequest $request): array
    {
        return [
            'slug' => $request->input('slug'),
            'title' => $request->input('title'),
            'year' => $request->input('year'),
            'category' => $request->input('category'),
            'status' => $request->input('status'),
            'summary' => $request->input('summary'),
            'description' => $request->input('description'),
            'cover_url' => $request->input('coverUrl'),
            'stack' => $request->input('stack', []),
            'features' => $request->input('features', []),
            'tech_decisions' => $request->input('techDecisions'),
            'featured' => $request->boolean('featured'),
            'published' => $request->boolean('published'),
            'order' => (int) $request->input('order', 0),
        ];
    }

    /**
     * Reescribe los links del proyecto desde el payload (estrategia de reemplazo).
     */
    private function syncLinks(Project $project, array $links): void
    {
        $project->links()->delete();

        foreach (array_values($links) as $i => $link) {
            $project->links()->create([
                'type' => $link['type'],
                'label' => $link['label'] ?? null,
                'url' => $link['url'],
                'order' => $i + 1,
            ]);
        }
    }
}
