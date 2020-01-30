<?php

namespace tpext\builder\common;

class Row
{
    protected $cols = [];

    /**
     * Undocumented function
     *
     * @param integer $size
     * @return void
     */
    public function column($size = 12)
    {
        $col = new Column($size);
        $this->cols[] = $col;
        return $col;
    }

    /**
     * Undocumented function
     *
     * @param integer $size
     * @return Form
     */
    public function form($size = 12)
    {
        return $this->column($size)->form();
    }

    /**
     * Undocumented function
     *
     * @param integer $size
     * @return Table
     */
    public function table($size = 12)
    {
        return $this->column($size)->table();
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getCols()
    {
        return $this->cols;
    }
}
