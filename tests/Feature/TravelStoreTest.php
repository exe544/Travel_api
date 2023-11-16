<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Travel;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class TravelStoreTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_create_travel_ok(): void
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', 'admin')->value('id'));
        $travel = Travel::factory()->make()->toArray();

        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels', $travel);

        $response->assertStatus(201);
        $this->assertDatabaseHas('travels', [
            'name' => $travel['name'],
            'slug' => Str::slug($travel['name']),
            'description'=>$travel['description']
        ]);
    }

    public function test_cant_create_travel_by_editor(): void
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', 'editor')->value('id'));
        $travel = Travel::factory()->make()->toArray();

        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels', $travel);

        $response->assertStatus(403);
        $response->assertJsonFragment(['message' => 'Sorry, you don\'t have rights to perform this action']);
        $this->assertDatabaseMissing('travels', [
            'name' => $travel['name'],
            'slug' => Str::slug($travel['name']),
            'description'=>$travel['description']
        ]);
    }

    public function test_cant_create_travel_with_not_unique_name(): void
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', 'admin')->value('id'));
        $travel = Travel::factory()->create();
        $newTravel = Travel::factory()->make(['name' => $travel->name])->toArray();

        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels', $newTravel);

        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => 'The name has already been taken.']);
    }

    public function test_cant_create_travel_by_visitor(): void
    {
        $travel = Travel::factory()->make()->toArray();

        $response = $this->postJson('/api/v1/admin/travels', $travel);

        $response->assertStatus(401);
        $response->assertJsonFragment(['message' => 'Unauthenticated.']);
    }
}
