<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TourStoreRequest;
use App\Http\Resources\TourResource;
use App\Models\Travel;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function store(Travel $travel, TourStoreRequest $request): TourResource
    {
        $tour = $travel->tour()->create($request->validated());

        return new TourResource($tour);
    }
}
