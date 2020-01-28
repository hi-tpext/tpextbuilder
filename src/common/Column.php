<?php

namespace tpext\builder\common;

use tpext\builder\common\Form;
use tpext\builder\common\Table;

class Column
{
    public $size = 12;

    public $elms = [];

    public function __construct($size = 12)
    {
        $this->size = $size;
    }

    public function form()
    {
        $form = new Form();
        $this->elms[] = $form;
        return $form;
    }

    public function table()
    {
        $table = new Table();
        $this->elms[] = $table;
        return $table;
    }

    public function getElms()
    {
        return $this->elms;
    }
}
