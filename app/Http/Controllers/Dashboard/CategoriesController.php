<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

class CategoriesController extends Controller
{
    public function index()
    {
        return view('dashboard.categories.index');
    }
}
