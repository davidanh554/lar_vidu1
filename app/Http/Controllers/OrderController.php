<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        return redirect()->route('home')->with('info', 'Tính năng lịch sử đơn hàng đang được cập nhật.');
    }

    public function show($id)
    {
        return redirect()->route('home')->with('info', 'Tính năng chi tiết đơn hàng đang được cập nhật.');
    }
}
