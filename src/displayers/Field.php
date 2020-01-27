<?php

namespace tpext\builder\displayer;

use tpext\builder\common\Renderable;

class Field implements Renderable
{
    protected $view = '';

    protected $value = '';

    protected $icon = '';

    protected $rules = '';

    protected $options = [];

    protected $editable = true;

    public function __construct($options = [])
    {
        $this->options = $options;
    }

    public function options($options)
    {
        $this->options = $options;
    }

    public function value($val)
    {
        $this->value = $val;
    }

    public function setEditable($editable = true)
    {
        $this->editable = $editable;
    }

    public function render()
    {
        return $this->value;
    }
}
