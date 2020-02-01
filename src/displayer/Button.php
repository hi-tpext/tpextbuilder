<?php

namespace tpext\builder\displayer;

class Button extends Field
{
    protected $view = 'button';

    protected $js = [
        '/assets/tpextbuilder/js/jquery.lyear.loading.js'
    ];

    protected $bottom = false;

    protected $size = [0, 12];

    protected $showLabel = false;

    protected $class = 'btn-default';

    protected $loading = false;

    public function loading($val = true)
    {
        $this->loading = $val;
    }

    public function render()
    {
        if ($this->loading) {
            $this->class .= ' btn-loading';
        }

        return parent::render();
    }
}
