<?php

namespace Tests\Feature\Board;

use App\Models\{Board, Column, Project, Organization, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BoardAndColumnApiTest extends TestCase
{
    use RefreshDatabase;

    private function login(): User
    {
        $u = User::factory()->create();
        $this->actingAs($u);
        return $u;
    }

    #[Test]
    public function boards_index_can_filter_by_project_and_eager_load_columns()
    {
        $this->login();

        $org     = Organization::factory()->create();
        $project = Project::factory()->for($org)->create();
        $board   = Board::factory()->for($project)->create();

        // at least one column so we can assert eager load structure
        Column::factory()->for($board)->create(['position' => 100]);

        // controller returns a plain array (no { data: [...] } wrapper)
        $resp = $this->getJson("/api/boards?project_id={$project->id}")
            ->assertOk()
            ->json();

        // should be an array of boards
        $this->assertIsArray($resp);

        // find our board in the response
        $item = collect($resp)->firstWhere('id', $board->id);
        $this->assertNotNull($item, 'response should contain the created board');

        // columns should be eager-loaded and include position
        $this->assertIsArray($item['columns'] ?? null);
        $this->assertNotEmpty($item['columns']);
        $this->assertArrayHasKey('id', $item['columns'][0]);
        $this->assertArrayHasKey('name', $item['columns'][0]);
        $this->assertArrayHasKey('position', $item['columns'][0]);
    }

    #[Test]
    public function columns_crud_and_reorder()
    {
        $this->login();

        $board = Board::factory()
            ->for(Project::factory()->for(Organization::factory()->create()))
            ->create();

        // store
        $colA = $this->postJson('/api/columns', [
                'board_id' => $board->id,
                'name'     => 'To Do',
                'position' => 100,
            ])
            ->assertCreated()
            ->json();

        $colB = $this->postJson('/api/columns', [
                'board_id' => $board->id,
                'name'     => 'Doing',
                'position' => 200,
            ])
            ->assertCreated()
            ->json();

        // update
        $this->putJson("/api/columns/{$colA['id']}", [
                'name' => 'Backlog',
            ])
            ->assertOk()
            ->assertJsonPath('name', 'Backlog');

        // reorder (swap) — payload wrapped under `columns`
        $this->patchJson(route('columns.reorder'), [
                'columns' => [
                    ['id' => $colA['id'], 'position' => 200],
                    ['id' => $colB['id'], 'position' => 100],
                ],
            ])
            ->assertOk();

        // destroy
        $this->deleteJson("/api/columns/{$colB['id']}")->assertNoContent();
    }
}