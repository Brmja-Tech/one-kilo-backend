<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

class ProductsController extends Controller
{
    public function index()
    {
        return view('dashboard.products.index');
    }
}
