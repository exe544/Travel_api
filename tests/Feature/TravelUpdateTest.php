<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Travel;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TravelUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_travel_ok(): void
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', 'editor')->value('id'));
        $travelOld = Travel::factory()->create();
        $travelUpdate = Travel::factory()->make()->toArray();

        $response = $this->actingAs($user)->putJson('/api/v1/admin/travels/' . $travelOld->id, $travelUpdate);

        $response->assertStatus(200);
        $this->assertDatabaseHas('travels', [
            'id' => $travelOld->id,
           'name' => $travelUpdate['name'],
           'description' => $travelUpdate['description'],
            'number_of_days' => $travelUpdate['number_of_days'],
            'is_public' => $travelUpdate['is_public']
        ]);

        $this->assertDatabaseMissing('travels', [
            'name' => $travelOld->name,
            'description' => $travelOld->description,
            'number_of_days' => $travelOld->number_of_days,
            'is_public' => $travelOld->is_public
        ]);
    }

    public function test_cant_be_updated_travel_by_admin_or_user(): void
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', 'admin')->value('id'));
        $travelOld = Travel::factory()->create();
        $travelUpdate = Travel::factory()->make()->toArray();

        $response = $this->actingAs($user)->putJson('/api/v1/admin/travels/' . $travelOld->id, $travelUpdate);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('travels', [
            'name' => $travelUpdate['name'],
            'description' => $travelUpdate['description'],
            'number_of_days' => $travelUpdate['number_of_days'],
            'is_public' => $travelUpdate['is_public']
        ]);

        $response = $this->putJson('/api/v1/admin/travels/' . $travelOld->id, $travelUpdate);
        $response->assertStatus(403);
        $this->assertDatabaseMissing('travels', [
            'name' => $travelUpdate['name'],
            'description' => $travelUpdate['description'],
            'number_of_days' => $travelUpdate['number_of_days'],
            'is_public' => $travelUpdate['is_public']
        ]);
    }

    public function test_cant_update_with_not_unique_name(): void
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', 'editor')->value('id'));
        $travel = Travel::factory()->create(['name' => 'used_name']);
        $travelOld = Travel::factory()->create();
        $travelUpdate = Travel::factory()->make(['name' => 'used_name'])->toArray();

        $response = $this->actingAs($user)->putJson('/api/v1/admin/travels/' . $travelOld->id, $travelUpdate);

        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => "The name has already been taken."]);
    }

    public function test_update_travel_using_old_record_name(): void
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', 'editor')->value('id'));

        $travelOld = Travel::factory()->create();
        $travelUpdate = Travel::factory()->make(['name' => $travelOld->name])->toArray();

        $response = $this->actingAs($user)->putJson('/api/v1/admin/travels/' . $travelOld->id, $travelUpdate);

        $response->assertStatus(200);
        $this->assertDatabaseHas('travels', [
            'name' => $travelOld['name'],
            'description' => $travelUpdate['description'],
            'number_of_days' => $travelUpdate['number_of_days'],
            'is_public' => $travelUpdate['is_public']
        ]);

        $this->assertDatabaseMissing('travels', [
            'description' => $travelOld['description'],
            'number_of_days' => $travelOld['number_of_days'],
            'is_public' => $travelOld['is_public']
        ]);
    }
}
