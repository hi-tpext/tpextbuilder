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
     * 获取一个表格
     *
     * @return Toolbar
     */
    public function toolbar()
    {
        $toolbar = new Toolbar();
        $this->elms[] = $toolbar;
        return $toolbar;
    }

    /**
     * Undocumented function
     *
     * @return Content
     */
    public function content()
    {
        $content = new Content();
        $this->elms[] = $content;
        return $content;
    }

    /**
     * Undocumented function
     *
     * @return Tab
     */
    public function tab()
    {
        $tab = new Tab();
        $this->elms[] = $tab;
        return $tab;
    }

    /**
     * 获取一新行
     *
     * @return Row
     */
    public function row()
    {
        $row = new Row();
        $this->elms[] = $row;
        return $row;
    }

    /**
     * Undocumented function
     *
     * @param Renderable $rendable
     * @return $this
     */
    public function append($rendable)
    {
        $this->elms[] = $rendable;
        return $this;
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

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function beforRender()
    {
        foreach ($this->elms as $elm) {
            $elm->beforRender();
        }

        return $this;
    }
}
