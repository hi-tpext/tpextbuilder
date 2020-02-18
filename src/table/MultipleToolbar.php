<?php

namespace tpext\builder\table;

use tpext\builder\common\Toolbar;

class MultipleToolbar extends Toolbar
{
    protected $useLayer = true;

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function useLayer($val)
    {
        $this->useLayer = $val;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function beforRender()
    {
        if (empty($this->elms)) {
            $this->buttons();
        }

        foreach ($this->elms as $elm) {

            $elm->useLayer($this->useLayer);
        }

        return parent::beforRender();
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function buttons()
    {
        $this->btnAdd();
        $this->btnDelete();
        $this->btnRefresh();
        $this->btnToggleSearch();

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @param string $label
     * @param string $class
     * @param string $icon
     * @param string $attr
     * @return $this
     */
    public function btnAdd($url = '', $label = '添加', $class = 'btn-primary', $icon = 'mdi-plus', $attr = '')
    {
        if (empty($url)) {
            $url = url('add');
        }
        $this->linkBtn('tool-add', $label)->href($url)->icon($icon)->class($class)->attr($attr);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $postUrl
     * @param string $label
     * @param string $class
     * @param string $icon
     * @param boolean $confirm
     * @param string $attr
     * @return $this
     */
    public function btnDelete($postUrl = '', $label = '删除', $class = 'btn-danger', $icon = 'mdi-delete', $confirm = true, $attr = '')
    {
        if (empty($postUrl)) {
            $postUrl = url('delete');
        }
        $this->linkBtn('tool-delete', $label)->postChecked($postUrl, $confirm)->class($class)->icon($icon)->attr($attr);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $postUrl
     * @param string $label
     * @param string $class
     * @param string $icon
     * @param boolean $confirm
     * @param string $attr
     * @return $this
     */
    public function btnDisable($postUrl = '', $label = '禁用', $class = 'btn-warning', $icon = 'mdi-block-helper', $confirm = true, $attr = '')
    {
        if (empty($postUrl)) {
            $postUrl = url('disable');
        }
        $this->linkBtn('tool-disable', $label)->postChecked($postUrl, $confirm)->class($class)->icon($icon)->attr($attr);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $postUrl
     * @param string $label
     * @param string $class
     * @param string $icon
     * @param boolean $confirm
     * @param string $attr
     * @return $this
     */
    public function btnEnable($postUrl = '', $label = '启用', $class = 'btn-success', $icon = 'mdi-check', $confirm = true, $attr = '')
    {
        if (empty($postUrl)) {
            $postUrl = url('enable');
        }
        $this->linkBtn('tool-enable', $label)->postChecked($postUrl, $confirm)->class($class)->icon($icon)->attr($attr);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param string $class
     * @param string $icon
     * @param boolean $confirm
     * @param string $attr
     * @return $this
     */
    public function btnRefresh($label = '', $class = 'btn-cyan', $icon = 'mdi-refresh', $attr = 'title="刷新"')
    {
        $this->linkBtn('tool-refresh', $label)->class($class)->icon($icon)->attr($attr);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param string $class
     * @param string $icon
     * @param boolean $confirm
     * @param string $attr
     * @return $this
     */
    public function btnToggleSearch($label = '', $class = 'btn-secondary', $icon = 'mdi-magnify', $attr = 'title="搜索"')
    {
        $this->linkBtn('tool-search', $label)->class($class)->icon($icon)->attr($attr);
        return $this;
    }

     /**
     * Undocumented function
     *
     * @param string $url
     * @param string $label
     * @param string $class
     * @param string $icon
     * @param string $attr
     * @return $this
     */
    public function btnLink($url, $label = '', $class = 'btn-secondary', $icon = 'mdi-checkbox-marked-outline', $attr = '')
    {
        $action = preg_replace('/.+?\/(\w+)\.?\w+$/', '$1', $url, -1, $count);

        if (!$count) {
            $action = mt_rand(10, 99);
        }

        $this->linkBtn('action-' . $action, $label)->href($url)->icon($icon)->class($class)->attr($attr);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @param string $label
     * @param string $class
     * @param string $icon
     * @param boolean $confirm
     * @param string $attr
     * @return $this
     */
    public function btnPostChecked($url, $label = '', $class = 'btn-secondary', $icon = 'mdi-checkbox-marked-outline', $confirm = true, $attr = '')
    {
        $action = preg_replace('/.+?\/(\w+)\.?\w+$/', '$1', $url, -1, $count);

        if (!$count) {
            $action = mt_rand(10, 99);
        }

        $this->linkBtn('bar-' . $action, $label)->postChecked($url, $confirm)->class($class)->icon($icon)->attr($attr);

        return $this;
    }
}
