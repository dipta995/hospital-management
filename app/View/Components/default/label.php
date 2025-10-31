<?php

namespace App\View\Components\default;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class label extends Component
{
    public $required;
    public $for;

    /**
     * Create a new component instance.
     */
    public function __construct($for = 'name', $required = true)
    {
        $this->required = $required;
        $this->for = $for;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.default.label');
    }
}
