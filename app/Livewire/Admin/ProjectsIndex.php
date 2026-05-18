<?php

namespace App\Livewire\Admin;

use App\Models\Project;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Throwable;

class ProjectsIndex extends Component
{
    use AuthorizesRequests;
    use WithFileUploads;
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $status = '';

    public ?int $editingId = null;

    public string $title = '';

    public string $slug = '';

    public string $description = '';

    public string $projectStatus = 'draft';

    public mixed $image = null;

    public ?string $currentImagePath = null;

    public ?int $confirmingDeleteId = null;

    public function mount(): void
    {
        $this->authorize('viewAny', Project::class);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatedTitle(): void
    {
        if (! $this->editingId) {
            $this->slug = Str::slug($this->title);
        }
    }

    public function create(): void
    {
        $this->authorize('create', Project::class);

        $this->resetForm();
    }

    public function edit(int $projectId): void
    {
        $project = Project::findOrFail($projectId);

        $this->authorize('update', $project);

        $this->editingId = $project->id;
        $this->title = $project->title;
        $this->slug = $project->slug;
        $this->description = $project->description ?? '';
        $this->projectStatus = $project->status;
        $this->currentImagePath = $project->image_path;
        $this->image = null;
    }

    public function save(): void
    {
        $project = $this->editingId ? Project::findOrFail($this->editingId) : null;

        $this->authorize($project ? 'update' : 'create', $project ?? Project::class);

        $this->slug = Str::slug($this->slug ?: $this->title);

        $data = $this->validate([
            'title' => ['required', 'string', 'min:3', 'max:160'],
            'slug' => ['required', 'string', 'max:180', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('projects', 'slug')->ignore($this->editingId)],
            'description' => ['nullable', 'string', 'max:5000'],
            'projectStatus' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:max_width=2400,max_height=2400'],
        ]);

        $previousImagePath = $this->currentImagePath;
        $newImagePath = null;

        if ($this->image) {
            $newImagePath = $this->image->store('projects', 'public');
        }

        try {
            DB::transaction(function () use ($data, $project, $newImagePath, $previousImagePath): void {
                Project::updateOrCreate(
                    ['id' => $this->editingId],
                    [
                        'title' => $data['title'],
                        'slug' => $data['slug'],
                        'description' => $data['description'] ?: null,
                        'status' => $data['projectStatus'],
                        'image_path' => $newImagePath ?? $previousImagePath,
                        'published_at' => $data['projectStatus'] === 'published' ? ($project?->published_at ?? now()) : null,
                    ],
                );
            });
        } catch (QueryException $exception) {
            $this->deleteStoredUpload($newImagePath);
            $this->throwSlugCollisionIfNeeded($exception);

            throw $exception;
        } catch (Throwable $exception) {
            $this->deleteStoredUpload($newImagePath);

            throw $exception;
        }

        if ($newImagePath && $previousImagePath && $newImagePath !== $previousImagePath) {
            Storage::disk('public')->delete($previousImagePath);
        }

        session()->flash('status', 'Proyecto guardado correctamente.');
        $this->resetForm();
    }

    public function confirmDelete(int $projectId): void
    {
        $this->authorize('delete', Project::findOrFail($projectId));

        $this->confirmingDeleteId = $projectId;
    }

    public function delete(): void
    {
        $projectId = $this->confirmingDeleteId;

        if (! $projectId) {
            return;
        }

        $project = Project::findOrFail($projectId);

        $this->authorize('delete', $project);

        if ($project->image_path) {
            Storage::disk('public')->delete($project->image_path);
        }

        $project->delete();

        session()->flash('status', 'Proyecto eliminado correctamente.');
        $this->confirmingDeleteId = null;
    }

    public function resetForm(): void
    {
        $this->reset([
            'editingId',
            'title',
            'slug',
            'description',
            'image',
            'currentImagePath',
        ]);
        $this->projectStatus = 'draft';
        $this->resetValidation();
    }

    private function deleteStoredUpload(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    private function throwSlugCollisionIfNeeded(QueryException $exception): void
    {
        if ((string) $exception->getCode() !== '23000') {
            return;
        }

        throw ValidationException::withMessages([
            'slug' => 'Este slug ya está registrado.',
        ]);
    }

    public function render()
    {
        $this->authorize('viewAny', Project::class);

        return view('livewire.admin.projects-index', [
            'projects' => Project::query()
                ->when($this->search, fn ($query) => $query->where(function ($query): void {
                    $query->where('title', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%");
                }))
                ->when($this->status, fn ($query) => $query->where('status', $this->status))
                ->latest()
                ->paginate(10),
        ]);
    }
}
