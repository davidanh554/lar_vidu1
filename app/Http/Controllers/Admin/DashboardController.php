<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalCustomers = User::where('role', 'customer')->count();
        
        return view('admin.dashboard', compact('totalProducts', 'totalCustomers'));
    }
}
