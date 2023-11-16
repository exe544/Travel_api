<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TravelStoreRequest;
use App\Http\Requests\TravelUpdateRequest;
use App\Http\Resources\TravelResource;
use App\Models\Travel;
use Illuminate\Http\Request;

/**
 * @group Admin endpoints
 */
class TravelController extends Controller
{
    /**
     * POST Travel
     *
     * Creates a new Travel record.
     *
     * @authenticated
     *
     * @response {"data":{"id":"996a36ca-2693-4901-9c55-7136e68d81d5","name":"My new travel 234","slug":"my-new-travel-234","description":"The second best journey ever!","number_of_days":"4","number_of_nights":3}}
     * @response 422 {"message":"The name has already been taken.","errors":{"name":["The name has already been taken."]}}
     */
    public function store(TravelStoreRequest $request): TravelResource
    {
        $validated = $request->validated();

        $travel = new Travel();
        $travel = $travel->fill($validated);
        $travel->save();

        return new TravelResource($travel);
    }

    /**
     * PUT Travel
     *
     * Updates new Travel record.
     *
     * @authenticated
     *
     * @response {"data":{"id":"996a36ca-2693-4901-9c55-7136e68d81d5","name":"My new travel 234","slug":"my-new-travel-234","description":"The second best journey ever!","number_of_days":"4","number_of_nights":3}}
     * @response 422 {"message":"The name has already been taken.","errors":{"name":["The name has already been taken."]}}
     */
    public function update(Travel $travel, TravelUpdateRequest $request): TravelResource
    {
        $travel->update($request->validated());

        return new TravelResource($travel->fresh());
    }
}
