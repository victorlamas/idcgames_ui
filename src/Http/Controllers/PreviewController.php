<?php

namespace IDCGames\UI\Http\Controllers;

use Illuminate\Routing\Controller;

class PreviewController extends Controller
{
    public function index()
    {
        return view('idcgames::preview');
    }
}
