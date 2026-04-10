<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Governorate;

class GovernoratesController extends Controller
{
    public function regions(Governorate $governorate)
    {
        return view('dashboard.regions.index', [
            'governorate' => $governorate->loadMissing('country:id,name'),
        ]);
    }
}
