<?php

namespace tpext\builder\table;

use tpext\builder\common\Renderable;
use tpext\builder\displayer\Text;
use tpext\builder\displayer\Field;

class Column implements Renderable
{
    protected $attributes = [];

    protected $name;

    protected $label;

    protected $displayer;

    protected $options = [];

    public function __construct($name, $label)
    {
        $this->name = $name;
        $this->label = $label;
        $this->displayer = new Field();
    }

    public function text($options = [])
    {
        $this->displayer = new Text();
        $this->options = $options;
    }

    public function options($options)
    {
        $this->options = array_merge($this->options, $options);
    }

    public function render()
    {

    }
}
