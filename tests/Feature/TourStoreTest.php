<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Tour;
use App\Models\Travel;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TourStoreTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_create_tour_ok(): void
    {
        $this->seed(RoleSeeder::class);
        $travel = Travel::factory()->create();
        $tour = Tour::factory()->make()->toArray();
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', 'admin')->value('id'));

        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels/' . $travel->id . '/tours', $tour);

        $response->assertStatus(201);
        $this->assertDatabaseHas('tours', [
            'name' => $tour['name'],
            'starting_date' => $tour['starting_date'],
            'ending_date' => $tour['ending_date'],
            'price' => $tour['price'] * 100,
        ]);
    }

    public function test_editor_cant_create_tour(): void
    {
        $this->seed(RoleSeeder::class);
        $travel = Travel::factory()->create();
        $tour = Tour::factory()->make()->toArray();
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', 'editor')->value('id'));

        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels/' . $travel->id . '/tours', $tour);

        $response->assertStatus(403);
        $response->assertJsonFragment(['message' => 'Sorry, you don\'t have rights to perform this action']);
    }

    public function test_cant_create_tour_with_unvalid_data(): void
    {
        $this->seed(RoleSeeder::class);
        $travel = Travel::factory()->create();
        $tour = Tour::factory()->make([
            'name' => rand(1, 5),
            'starting_date' => now()->subDays()->format('Y-m-d'),
            'ending_date' => now()->format('Y-m-d'),
            'price' => 1235,
            ])->toArray();

        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', 'admin')->value('id'));

        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels/' . $travel->id . '/tours', $tour);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'message' =>'The name field must be a string. (and 2 more errors)',
        ]);
    }
}
