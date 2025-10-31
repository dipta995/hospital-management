<?php

namespace App\View\Components\default;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class select extends Component
{
    public $name;
    public $class;
    public $id;
    /**
     * Create a new component instance.
     */
    public function __construct($name, $class, $id = null)
    {
        $this->name = $name;
        $this->class = $class;
        $this->id = $id ?? $name;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.default.select');
    }
}
