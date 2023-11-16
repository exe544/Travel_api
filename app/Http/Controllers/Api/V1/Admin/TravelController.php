<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TravelStoreRequest;
use App\Http\Requests\TravelUpdateRequest;
use App\Http\Resources\TravelResource;
use App\Models\Travel;
use Illuminate\Http\Request;

class TravelController extends Controller
{
    public function store(TravelStoreRequest $request): TravelResource
    {
        $validated = $request->validated();

        $travel = new Travel();
        $travel = $travel->fill($validated);
        $travel->save();

        return new TravelResource($travel);
    }

    public function update(Travel $travel, TravelUpdateRequest $request): TravelResource
    {
        $travel->update($request->validated());

        return new TravelResource($travel->fresh());
    }
}
