<?php

namespace IDCGames\UI\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class Navbar extends Component
{
    public array $services;
    public string $currentService;
    public ?object $user;
    public bool $isLauncher;

    public function __construct(string $active = '')
    {
        $this->services       = config('idcgames-ui.services', []);
        $this->currentService = $active;
        $this->user           = Auth::user();
        $this->isLauncher     = static::detectLauncher();
    }

    public function render()
    {
        // En el launcher se oculta la barra — la lógica SSO sigue intacta
        if ($this->isLauncher) {
            return '';
        }
        return view('idcgames::components.navbar');
    }

    public static function detectLauncher(): bool
    {
        $ua = request()->userAgent() ?? '';
        return stripos($ua, 'idclauncher') !== false;
    }
}
