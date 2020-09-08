<?php

namespace tpext\builder\common;

use tpext\builder\common\Form;
use tpext\builder\common\Table;
use tpext\builder\inface\Renderable;
use tpext\builder\traits\HasDom;
use tpext\builder\tree\ZTree;
use tpext\common\ExtLoader;

class Column
{
    use HasDom;

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
        ExtLoader::trigger('tpext_create_form', $form);
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
        ExtLoader::trigger('tpext_create_table', $table);
        $this->elms[] = $table;
        return $table;
    }

    /**
     * 获取一个Toolbar
     *
     * @return Toolbar
     */
    public function toolbar()
    {
        $toolbar = new Toolbar();
        ExtLoader::trigger('tpext_create_toolbar', $toolbar);
        $this->elms[] = $toolbar;
        return $toolbar;
    }

    /**
     * 获取一个ZTree
     *
     * @return ZTree
     */
    public function tree()
    {
        $tree = new ZTree();
        ExtLoader::trigger('tpext_create_ztree', $tree);
        $this->elms[] = $tree;
        return $tree;
    }

    /**
     * 获取一个自定义内容
     *
     * @return Content
     */
    public function content()
    {
        $content = new Content();
        ExtLoader::trigger('tpext_create_content', $content);
        $this->elms[] = $content;
        return $content;
    }

    /**
     * 获取一个 tab
     *
     * @return Tab
     */
    public function tab()
    {
        $tab = new Tab();
        ExtLoader::trigger('tpext_create_tab', $tab);
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
        ExtLoader::trigger('tpext_create_row', $row);
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
