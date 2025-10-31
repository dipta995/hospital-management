<?php

namespace App\View\Components\default;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class input extends Component
{
    public $name;
    public $class;
    public $id;
    public $type;
    public $data;

    /**
     * Create a new component instance.
     */
    public function __construct($name, $class = 'form-control', $id, $type = 'text', $data = null)
    {
        $this->name = $name;
        $this->class = $class;
        $this->id = $id;
        $this->type = $type;
        $this->data = $data;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.default.input');
    }
}
