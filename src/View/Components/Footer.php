<?php

namespace IDCGames\UI\View\Components;

use Illuminate\View\Component;

class Footer extends Component
{
    public array $services;

    public function __construct()
    {
        $this->services = config('idcgames-ui.services', []);
    }

    public function render()
    {
        return view('idcgames::components.footer');
    }
}
