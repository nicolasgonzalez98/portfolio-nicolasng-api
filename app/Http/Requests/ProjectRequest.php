<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Las rutas ya están detrás de auth:sanctum (único admin).
        return true;
    }

    public function rules(): array
    {
        $projectId = $this->route('project')?->id;

        return [
            'slug' => [
                'required', 'string', 'max:255', 'alpha_dash',
                Rule::unique('projects', 'slug')->ignore($projectId),
            ],
            'title' => ['required', 'string', 'max:255'],
            'year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'category' => ['required', Rule::in(['DESTACADO', 'JS_REACT', 'PYTHON', 'TRABAJO_PROFESIONAL'])],
            'status' => ['nullable', 'string', 'max:255'],
            'summary' => ['required', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'coverUrl' => ['nullable', 'string', 'max:1000'],

            'stack' => ['array'],
            'stack.*' => ['string', 'max:100'],

            'features' => ['array'],
            'features.*' => ['string', 'max:1000'],

            'techDecisions' => ['nullable', 'array'],
            'techDecisions.*.title' => ['required_with:techDecisions', 'string', 'max:255'],
            'techDecisions.*.description' => ['required_with:techDecisions', 'string', 'max:2000'],

            'featured' => ['boolean'],
            'published' => ['boolean'],
            'order' => ['integer'],

            'links' => ['array'],
            'links.*.type' => ['required', Rule::in(['WEBSITE', 'REPO', 'DEMO'])],
            'links.*.label' => ['nullable', 'string', 'max:255'],
            'links.*.url' => ['required', 'url', 'max:1000'],
        ];
    }
}
