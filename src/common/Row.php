<?php

namespace tpext\builder\common;

class Row
{
    protected $cols = [];

    public function column($size = 12)
    {
        $col = new Column($size);
        $this->cols[] = $col;
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

    public function getCols()
    {
        return $this->cols;
    }
}
