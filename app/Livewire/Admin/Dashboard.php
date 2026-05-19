<?php

namespace App\Livewire\Admin;

use App\Models\Project;
use App\Models\User;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.admin.dashboard', [
            'usersCount' => User::count(),
            'projectsCount' => config('starter.modules.projects') ? Project::count() : 0,
            'publishedCount' => config('starter.modules.projects') ? Project::where('status', 'published')->count() : 0,
            'latestProjects' => config('starter.modules.projects') ? Project::latest()->take(5)->get() : collect(),
        ]);
    }
}
