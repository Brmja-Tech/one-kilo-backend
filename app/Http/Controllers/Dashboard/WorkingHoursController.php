<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Governorate;
use App\Models\WorkingHour;

class WorkingHoursController extends Controller
{
    public function regions(WorkingHour $workingHour)
    {
        return view('dashboard.regions.index', [
            'governorate' => $governorate->loadMissing('country:id,name'),
        ]);
    }
}
