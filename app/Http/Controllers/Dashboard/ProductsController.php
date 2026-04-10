<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\View\View;

class ProductsController extends Controller
{
    public function index()
    {
        return view('dashboard.products.index');
    }

    public function skus(Product $product): View
    {
        return view('dashboard.products.skus', [
            'product' => $product,
        ]);
    }
}
