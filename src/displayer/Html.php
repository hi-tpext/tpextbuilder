<?php

namespace tpext\builder\displayer;

class Html extends Text
{
    protected $view = 'html';

    public function __construct($html = '')
    {
        $this->size([0, 12]);

        $this->value = $html;
    }
}
