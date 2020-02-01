<?php

namespace tpext\builder\common;

use tpext\builder\common\Form;
use tpext\builder\common\Table;

class Column
{
    public $size = 12;

    protected $elms = [];

    public function __construct($size = 12)
    {
        $this->size = $size;
    }

    /**
     * 获取一个form
     *
     * @return Form
     */

    public function form()
    {
        $form = new Form();
        $this->elms[] = $form;
        return $form;
    }

    /**
     * 获取一个表格
     *
     * @return Table
     */
    public function table()
    {
        $table = new Table();
        $this->elms[] = $table;
        return $table;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getElms()
    {
        return $this->elms;
    }

    /**
     * Undocumented function
     *
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }

    public function beforRender()
    {
        foreach ($this->elms as $elm) {
            $elm->beforRender();
        }
    }
}
