<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

class CouponsController extends Controller
{
    public function index()
    {
        return view('dashboard.coupons.index');
    }
}
