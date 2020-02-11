<?php

namespace tpext\builder\toolbar;

class Html extends Bar
{
    protected $view = 'html';

    public function __construct($html)
    {
        $this->label = $html;
    }
}
