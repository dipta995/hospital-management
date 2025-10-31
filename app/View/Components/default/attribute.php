<?php

namespace App\View\Components\default;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class attribute extends Component
{
    public $class;
    public $href;
    /**
     * Create a new component instance.
     */
    public function __construct($class = 'btn-info',$href = '#')
    {
        $this->class = $class;
        $this->$href = $href;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.default.attribute');
    }
}
