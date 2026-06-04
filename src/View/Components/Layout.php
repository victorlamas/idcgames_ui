<?php

namespace IDCGames\UI\View\Components;

use Illuminate\View\Component;

class Layout extends Component
{
    public function __construct(
        public string $title = '',
        public string $description = '',
        public bool   $withNavbar = true,
        public bool   $withFooter = true,
        public string $bodyClass = '',
    ) {}

    public function render()
    {
        return view('idcgames::components.layout');
    }
}
