<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ProjectController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        abort_unless(config('starter.modules.api') && config('starter.modules.projects'), 404);
        abort_unless(request()->user()?->tokenCan('projects:read'), Response::HTTP_FORBIDDEN);

        return ProjectResource::collection(
            Project::query()
                ->where('status', 'published')
                ->latest('published_at')
                ->paginate(15)
        );
    }

    public function show(Project $project): ProjectResource
    {
        abort_unless(config('starter.modules.api') && config('starter.modules.projects'), 404);
        abort_unless(request()->user()?->tokenCan('projects:read'), Response::HTTP_FORBIDDEN);
        abort_unless($project->status === 'published', 404);

        return ProjectResource::make($project);
    }
}
