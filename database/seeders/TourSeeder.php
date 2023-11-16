<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Tour;
use App\Models\Travel;
use Database\Factories\TourFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $travel = Travel::factory()->create(['name' => 'Japan']);

        Tour::factory(30)->create(['travel_id' => $travel->id]);
    }
}
