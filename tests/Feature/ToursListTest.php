<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Tour;
use App\Models\Travel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ToursListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */

    public function test_tours_index_returns_correct_tours_by_travel_slug(): void
    {
        $travel = Travel::factory()->create();
        $tour = Tour::factory()->create(['travel_id' => $travel->id]);
        $response = $this->get('/api/v1/travels/' . $travel->slug . '/tours');

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $tour->id]);
        $response->assertJsonCount(1, 'data');
    }

    public function test_tour_index_return_correct_price(): void
    {
        $travel = Travel::factory()->create();
        $tour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 123.45
        ]);

        $response = $this->get('/api/v1/travels/' . $travel->slug . '/tours');

        $response->assertStatus(200);
        $response->assertJsonFragment(['price' => '123.45']);
        $response->assertJsonCount(1, 'data');
    }

    public function test_tour_index_return_with_pagination(): void
    {
        $travel = Travel::factory()->create();
        $tour = Tour::factory(16)->create(['travel_id' => $travel->id]);

        $response = $this->get('/api/v1/travels/' . $travel->slug . '/tours');

        $response->assertStatus(200);
        $response->assertJsonPath('meta.last_page', 2);
        //basic pagination is 15 elements per page
        $response->assertJsonCount(15, 'data');
    }

    public function test_tour_index_with_price_filter(): void
    {
        $travel = Travel::factory()->create();
        $tourCheap = Tour::factory()->create(['travel_id' => $travel->id, 'price' => 100]);
        $tourMiddle = Tour::factory()->create(['travel_id' => $travel->id, 'price' => 150]);
        $tourExpensive = Tour::factory()->create(['travel_id' => $travel->id, 'price' => 202]);

        $endpoint = '/api/v1/travels/' . $travel->slug . '/tours';

        //show all 3 tour
        $response = $this->get($endpoint . '?priceFrom=' . $tourCheap->price . '&priceTo=' . $tourExpensive->price);

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');

        //show results between range
        $response = $this->get($endpoint . '?priceFrom=101&priceTo=200');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $tourMiddle->id]);

        //show results with "To" limit
        $response = $this->get($endpoint . '?priceTo=200');

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['id' => $tourMiddle->id]);
        $response->assertJsonFragment(['id' => $tourCheap->id]);

        //show results with "From" limit
        $response = $this->get($endpoint . '?priceFrom=101');

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['id' => $tourMiddle->id]);
        $response->assertJsonFragment(['id' => $tourExpensive->id]);
    }

    public function test_tour_index_with_date_filter(): void
    {
        $travel = Travel::factory()->create();
        $tourEarly = Tour::factory()->create([
            'travel_id' => $travel->id,
            'starting_date' => now()->addDays(2)->format('Y-m-d'),
            'ending_date' => now()->addDays(4)->format('Y-m-d')
        ]);

        $tourLater = Tour::factory()->create([
            'travel_id' => $travel->id,
            'starting_date' => now()->addDays(3)->format('Y-m-d'),
            'ending_date' => now()->addDays(5)->format('Y-m-d')
        ]);

        $endpoint = '/api/v1/travels/' . $travel->slug . '/tours';
        //show all
        $response = $this->get($endpoint . '?dateFrom=' . now()->format('Y-m-d') . '&dateTo=' . now()->addDays(6)->format('Y-m-d'));

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');


        //check dateTo Limit
        $response = $this->get($endpoint . '?dateTo=' . now()->addDays(4)->format('Y-m-d'));
        $response->assertJsonFragment(['id' => $tourEarly->id]);
        $response->assertJsonMissing(['id' => $tourLater->id]);

        //check dateFrom Limit
        $response = $this->get($endpoint . '?dateFrom=' . $tourLater->starting_date);
        $response->assertJsonFragment(['id' => $tourLater->id]);
        $response->assertJsonMissing(['id' => $tourEarly->id]);
    }

    public function test_tour_index_with_sortBy_price(): void
    {
        $travel = Travel::factory()->create();
        $tourCheap = Tour::factory()->create(['travel_id' => $travel->id, 'price' => 100]);
        $tourExpensive = Tour::factory()->create(['travel_id' => $travel->id, 'price' => 200]);
        $tourTooExpensive = Tour::factory()->create(['travel_id' => $travel->id, 'price' => 300]);

        $endpoint = '/api/v1/travels/' . $travel->slug . '/tours';

        //default order
        $response = $this->get($endpoint . '?sortBy=price');

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id', $tourCheap->id);
        $response->assertJsonPath('data.1.id', $tourExpensive->id);
        $response->assertJsonPath('data.2.id', $tourTooExpensive->id);

        //desc order
        $response = $this->get($endpoint . '?sortBy=price&sortOrder=desc');
        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id', $tourTooExpensive->id);
        $response->assertJsonPath('data.1.id', $tourExpensive->id);
        $response->assertJsonPath('data.2.id', $tourCheap->id);
    }

    public function test_negative_tour_index_with_sortBy_Not_by_price(): void
    {
        $travel = Travel::factory()->create();
//        $tourCheap = Tour::factory()->create(['travel_id' => $travel->id, 'price' => 100]);
//        $tourExpensive = Tour::factory()->create(['travel_id' => $travel->id, 'price' => 200]);
//        $tourTooExpensive = Tour::factory()->create(['travel_id' => $travel->id, 'price' => 300]);

        $endpoint = '/api/v1/travels/' . $travel->slug . '/tours';

        $response = $this->getJson($endpoint . '?sortBy=fwef');
        $response->assertStatus(422);
    }

}
