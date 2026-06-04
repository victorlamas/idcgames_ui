<?php

namespace IDCGames\UI\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class Navbar extends Component
{
    public array $services;
    public string $currentService;
    public ?object $user;

    public function __construct(string $active = '')
    {
        $this->services       = config('idcgames-ui.services', []);
        $this->currentService = $active;
        $this->user           = Auth::user();
    }

    public function render()
    {
        return view('idcgames::components.navbar');
    }
}
