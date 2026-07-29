<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->smallInteger('year')->nullable();
            // DESTACADO | JS_REACT | PYTHON | TRABAJO_PROFESIONAL
            $table->string('category');
            // "En producción", "En desarrollo", etc. (opcional)
            $table->string('status')->nullable();
            $table->string('summary');
            $table->text('description')->nullable();
            // Portada del listado. cover_key es null si es un asset local/legacy (no vive en R2).
            $table->string('cover_url')->nullable();
            $table->string('cover_key')->nullable();
            $table->json('stack');
            $table->json('features');
            // [{ "title": "...", "description": "..." }] — solo en proyectos destacados
            $table->json('tech_decisions')->nullable();
            $table->boolean('featured')->default(false);
            $table->boolean('published')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
