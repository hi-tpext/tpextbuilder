<?php

namespace tpext\builder\common;

class Column implements Renderable
{
    protected $size = 12;

    protected $elms = [];

    public function __construct($size = 12)
    {
        $this->size = $size;
    }

    public function form()
    {
        return 0;
    }

    public function table()
    {
        return 0;
    }

    public function render()
    {
        
    }
}
