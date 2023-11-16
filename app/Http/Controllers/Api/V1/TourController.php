<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TourIndexRequest;
use App\Http\Resources\TourResourceCollection;
use App\Http\Resources\TravelResourceCollection;
use App\Models\Travel;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function index(Travel $travel, TourIndexRequest $request)
    {
        $tours = $travel->tour()
            ->when($request->priceFrom, function ($query) use ($request) {
                $query->where('price', '>=',  $request->priceFrom * 100);
            })
            ->when($request->priceTo, function ($query) use ($request) {
                $query->where('price', '<=', $request->priceTo * 100);
            })
            ->when($request->dateFrom, function ($query) use ($request){
                $query->where('starting_date', '>=', $request->dateFrom);
            })
            ->when($request->dateTo, function ($query) use ($request) {
                $query->where('ending_date', '<=', $request->dateTo);
            })
            ->when($request->sortBy, function ($query) use ($request) {
                $query->orderBy($request->sortBy, $request->sortOrder ?? 'asc');
            })
            ->orderBy('starting_date', 'asc')
            ->paginate();

        return new TourResourceCollection($tours);
    }

}
