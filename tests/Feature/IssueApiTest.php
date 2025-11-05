<?php

namespace Tests\Feature\Issue;

use App\Models\{Issue, Column, Board, Project, Organization, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class IssueApiTest extends TestCase
{
    use RefreshDatabase;

    private function login(): User { $u = User::factory()->create(); $this->actingAs($u); return $u; }

    private function seedBoard(): array
    {
        $org = Organization::factory()->create();
        $project = Project::factory()->for($org)->create();
        $board = Board::factory()->for($project)->create();
        $todo = Column::factory()->for($board)->create(['name' => 'To Do', 'position' => 1]);
        $doing = Column::factory()->for($board)->create(['name' => 'Doing', 'position' => 2]);
        return compact('org','project','board','todo','doing');
    }

    #[Test]
    public function index_supports_filters_and_eager_loads()
    {
        $this->login();
        $ctx = $this->seedBoard();

        $i1 = Issue::factory()->for($ctx['project'])->for($ctx['todo'])->create(['title' => 'Fix login', 'priority' => 'high']);
        $i2 = Issue::factory()->for($ctx['project'])->for($ctx['doing'])->create(['title' => 'Write docs', 'priority' => 'low']);

        $this->getJson("/api/issues?project_id={$ctx['project']->id}&priority=high&search=login")
             ->assertOk()
             ->assertJsonFragment(['id' => $i1->id])
             ->assertJsonMissing(['id' => $i2->id])
             ->assertJsonStructure(['data' => [[ 'id', 'assignee', 'reporter', 'column' ]]]);
    }

    #[Test]
    public function store_update_move_destroy_issue()
    {
        $user = $this->login();
        $ctx = $this->seedBoard();

        // store
        $res = $this->postJson('/api/issues', [
            'project_id'  => $ctx['project']->id,
            'column_id'   => $ctx['todo']->id,
            'title'       => 'Implement CSRF',
            'description' => 'Add Sanctum CSRF flow',
            'priority'    => 'medium',
            'assignee_id' => $user->id,
        ])->assertCreated();

        $issueId = $res->json('id');

        // update
        $this->putJson("/api/issues/{$issueId}", ['title' => 'Implement CSRF flow'])
             ->assertOk()->assertJsonPath('title', 'Implement CSRF flow');

        // move
        $this->patchJson("/api/issues/{$issueId}/move", ['column_id' => $ctx['doing']->id, 'order' => 1])
             ->assertOk();

        // destroy
        $this->deleteJson("/api/issues/{$issueId}")->assertNoContent();
    }
}