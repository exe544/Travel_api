<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TravelResource;
use App\Http\Resources\TravelResourceCollection;
use App\Models\Travel;
use Illuminate\Http\Request;

class  TravelController extends Controller
{
    public function index(): TravelResourceCollection
    {
        $travels = Travel::where('is_public', true)->paginate();

        return new TravelResourceCollection($travels);
    }
}
