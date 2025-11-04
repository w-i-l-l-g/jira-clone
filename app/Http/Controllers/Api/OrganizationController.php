<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Organization, User};
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index(Request $r)
    {
        // list orgs current user belongs to
        return $r->user()->organizations()->paginate(20);
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:organizations,slug',
        ]);

        $org = Organization::create([
            'owner_id' => $r->user()->id,
            'name' => $data['name'],
            'slug' => $data['slug'],
        ]);

        // attach creator as owner
        $org->users()->attach($r->user()->id, ['role' => 'owner']);

        return response()->json($org, 201);
    }

    public function show(Organization $org)
    {
        return $org->load('projects:id,organization_id,name,key');
    }

    public function update(Request $r, Organization $org)
    {
        $data = $r->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:organizations,slug,' . $org->id,
        ]);
        $org->update($data);
        return $org;
    }

    // stubs (safe to add later)
    public function members(Organization $org) { return $org->users()->select('users.id','name','email')->get(); }
    public function invite(Request $r, Organization $org) { return response()->json(['todo' => true], 202); }
}