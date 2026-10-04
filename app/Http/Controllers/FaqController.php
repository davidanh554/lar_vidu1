<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Hiển thị trang Câu hỏi thường gặp (FAQ)
     */
    public function index()
    {
        return view('pages.faq');
    }
}
