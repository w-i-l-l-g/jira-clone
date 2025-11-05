<?php

namespace Tests\Feature\Org;

use App\Models\{Organization, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrganizationApiTest extends TestCase
{
    use RefreshDatabase;

    private function login(): User
    {
        $u = User::factory()->create(['password' => bcrypt('password')]);
        $this->actingAs($u);
        return $u;
    }

    #[Test]
    public function index_returns_only_orgs_user_belongs_to()
    {
        $u = $this->login();
        $mine = Organization::factory()->create();
        $mine->users()->attach($u->id, ['role' => 'member']);
        $other = Organization::factory()->create();

        $this->getJson('/api/orgs')
            ->assertOk()
            ->assertJsonFragment(['id' => $mine->id])
            ->assertJsonMissing(['id' => $other->id]);
    }

    #[Test]
    public function store_creates_org_and_adds_owner_membership()
    {
        $u = $this->login();

        $payload = ['name' => 'Acme', 'slug' => 'acme'];
        $res = $this->postJson('/api/orgs', $payload)
            ->assertCreated()
            ->assertJsonFragment(['name' => 'Acme', 'slug' => 'acme']);

        $orgId = $res->json('id');
        $this->assertDatabaseHas('organizations', ['id' => $orgId, 'owner_id' => $u->id]);
        $this->assertDatabaseHas('organization_user', ['organization_id' => $orgId, 'user_id' => $u->id]);
    }

    #[Test]
    public function show_returns_org()
    {
        $u = $this->login();
        $org = Organization::factory()->create();
        $org->users()->attach($u->id, ['role' => 'member']);

        $this->getJson("/api/orgs/{$org->id}")
            ->assertOk()
            ->assertJsonPath('id', $org->id);
    }

    #[Test]
    public function update_validates_unique_slug()
    {
        $u = $this->login();

        $a = Organization::factory()->create(['slug' => 'a']); $a->users()->attach($u->id);
        $b = Organization::factory()->create(['slug' => 'b']); $b->users()->attach($u->id);

        $this->putJson("/api/orgs/{$b->id}", ['slug' => 'a'])
            ->assertStatus(422);
    }

    #[Test]
    public function members_returns_basic_fields()
    {
        $u = $this->login();
        $org = Organization::factory()->create();
        $org->users()->attach($u->id, ['role' => 'member']);

        $this->getJson("/api/orgs/{$org->id}/members")
             ->assertOk()
             ->assertJsonFragment(['id' => $u->id, 'email' => $u->email]);
    }
}