<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class Buscador extends Component
{
    public $param;
    public $placeholder;

    public function __construct($param = 'search', $placeholder = 'Buscar...')
    {
        $this->param = $param;
        $this->placeholder = $placeholder;
    }

    public function render(): View|Closure|string
    {
        return view('components.buscador');
    }
}

