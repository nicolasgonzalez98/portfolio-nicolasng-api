<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectListResource;
use App\Http\Resources\ProjectResource;
use App\Models\Project;

class ProjectController extends Controller
{
    /**
     * Lista de proyectos publicados, ordenados para la grilla del front.
     */
    public function index()
    {
        $projects = Project::published()
            ->orderBy('order')
            ->get();

        return ProjectListResource::collection($projects);
    }

    /**
     * Detalle de un proyecto por slug (con links e imágenes).
     */
    public function show(Project $project)
    {
        abort_unless($project->published, 404);

        $project->load(['links', 'images']);

        return new ProjectResource($project);
    }
}
