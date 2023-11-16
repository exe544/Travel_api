<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Travel;

class TravelObserver
{
    /**
     * Handle the Travel "created" event.
     */
    public function creating(Travel $travel): void
    {
        $travel->slug = str($travel->name)->slug();
    }
}
