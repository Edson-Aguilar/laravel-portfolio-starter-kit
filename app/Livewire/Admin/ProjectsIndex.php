<?php

namespace App\Livewire\Admin;

use App\Models\Project;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ProjectsIndex extends Component
{
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
        $this->resetForm();
    }

    public function edit(int $projectId): void
    {
        $project = Project::findOrFail($projectId);

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
        $data = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('projects', 'slug')->ignore($this->editingId)],
            'description' => ['nullable', 'string', 'max:5000'],
            'projectStatus' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $imagePath = $this->currentImagePath;

        if ($this->image) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            $imagePath = $this->image->store('projects', 'public');
        }

        Project::updateOrCreate(
            ['id' => $this->editingId],
            [
                'title' => $data['title'],
                'slug' => Str::slug($data['slug']),
                'description' => $data['description'] ?: null,
                'status' => $data['projectStatus'],
                'image_path' => $imagePath,
                'published_at' => $data['projectStatus'] === 'published' ? now() : null,
            ],
        );

        session()->flash('status', 'Project saved successfully.');
        $this->resetForm();
    }

    public function delete(int $projectId): void
    {
        $project = Project::findOrFail($projectId);

        if ($project->image_path) {
            Storage::disk('public')->delete($project->image_path);
        }

        $project->delete();

        session()->flash('status', 'Project deleted successfully.');
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

    public function render()
    {
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
