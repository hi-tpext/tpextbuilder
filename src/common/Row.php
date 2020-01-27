<?php

namespace tpext\builder\common;

class Row implements Renderable
{

    protected $cols = [];

    protected $__col__ = null;

    public function column($size = 12)
    {
        $col = new Column($size);
        $this->cols[] = $col;
        $this->__col__ = $col;
        return $col;
    }

    public function form($size = 12)
    {
        return $this->column($size)->form();
    }

    public function table($size = 12)
    {
        return $this->column($size)->table();
    }

    public function render()
    {
        
    }
}
