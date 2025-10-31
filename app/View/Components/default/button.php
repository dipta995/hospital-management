<?php

namespace App\View\Components\default;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class button extends Component
{
    public $class;
    public $type;
    /**
     * Create a new component instance.
     */
    public function __construct($class = 'btn-info', $type = 'submit')
    {
        $this->class = $class;
        $this->type = $type;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.default.button');
    }
}
