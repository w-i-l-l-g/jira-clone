<?php

namespace Tests\Feature\Project;

use App\Models\{Organization, Project, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProjectApiTest extends TestCase
{
    use RefreshDatabase;

    private function login(): User { $u = User::factory()->create(); $this->actingAs($u); return $u; }

    #[Test]
    public function index_filters_by_organization_and_includes_issue_count()
    {
        $this->login();
        $orgA = Organization::factory()->create();
        $orgB = Organization::factory()->create();
        $a1 = Project::factory()->for($orgA)->create();
        $b1 = Project::factory()->for($orgB)->create();

        $this->getJson("/api/projects?organization_id={$orgA->id}")
             ->assertOk()
             ->assertJsonFragment(['id' => $a1->id])
             ->assertJsonMissing(['id' => $b1->id])
             ->assertJsonStructure(['data' => [[ 'id', 'issues_count' ]]]);
    }

    #[Test]
    public function store_validates_and_creates()
    {
        $this->login();
        $org = Organization::factory()->create();

        $res = $this->postJson('/api/projects', [
            'organization_id' => $org->id,
            'name'            => 'Core',
            'key'             => 'CORE', // ← REQUIRED by controller
            'description'     => 'Main project',
        ])->assertCreated();

        $this->assertDatabaseHas('projects', [
            'id'              => $res->json('id'),
            'organization_id' => $org->id,
            'name'            => 'Core',
            'key'             => 'CORE',
        ]);
    }

    #[Test]
    public function show_update_destroy()
    {
        $this->login();
        $project = Project::factory()->create();

        $this->getJson("/api/projects/{$project->id}")
             ->assertOk()->assertJsonPath('id', $project->id);

        $this->putJson("/api/projects/{$project->id}", ['name' => 'Renamed'])
             ->assertOk()->assertJsonPath('name', 'Renamed');

        $this->deleteJson("/api/projects/{$project->id}")
             ->assertNoContent();

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }
}