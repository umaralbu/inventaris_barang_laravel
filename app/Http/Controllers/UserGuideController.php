<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class UserGuideController extends Controller
{
    public function index(): View
    {
        return view('guide.index');
    }
}
