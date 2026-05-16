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
            'projectsCount' => Project::count(),
            'publishedCount' => Project::where('status', 'published')->count(),
            'latestProjects' => Project::latest()->take(5)->get(),
        ]);
    }
}
