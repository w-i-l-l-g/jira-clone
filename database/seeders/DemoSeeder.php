<?php

namespace Database\Seeders;

use App\Models\{Organization, Project, Board};
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'password' => bcrypt('password'),
        ]);

        $org = Organization::create([
            'owner_id' => $user->id,
            'name' => 'Demo Org',
            'slug' => 'demo-'.Str::random(5),
        ]);
        $org->users()->attach($user->id, ['role' => 'owner']);

        $project = $org->projects()->create([
            'name' => 'Demo Project',
            'key' => 'DEMO',
            'description' => 'Seeded project',
        ]);

        $board = $project->boards()->create(['name' => 'Board']);
        $columns = $board->columns()->createMany([
            ['name' => 'Backlog',     'position' => 100],
            ['name' => 'In Progress', 'position' => 200],
            ['name' => 'Review',      'position' => 300],
            ['name' => 'Done',        'position' => 400],
        ]);

        // a couple of issues
        $c1 = $columns[0];
        $project->issues()->create([
            'column_id'   => $c1->id,
            'reporter_id' => $user->id,
            'assignee_id' => $user->id,
            'key'         => $project->nextIssueKey(),
            'title'       => 'Set up Kanban board',
            'description' => 'Create columns and enable DnD',
            'priority'    => 'medium',
        ]);
        $project->issues()->create([
            'column_id'   => $c1->id,
            'reporter_id' => $user->id,
            'assignee_id' => null,
            'key'         => $project->nextIssueKey(),
            'title'       => 'Add issue drawer',
            'description' => 'Right-side details panel',
            'priority'    => 'high',
        ]);
    }
}