<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Country;

class CountriesController extends Controller
{
    public function index()
    {
        return view('dashboard.countries.index');
    }

    public function governorates(Country $country)
    {
        return view('dashboard.countries.governorates-index', [
            'country' => $country,
        ]);
    }
}
