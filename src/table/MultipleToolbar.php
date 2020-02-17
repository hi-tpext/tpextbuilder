<?php

namespace tpext\builder\table;

use tpext\builder\common\Toolbar;

class MultipleToolbar extends Toolbar
{
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

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param string $class
     * @return $this
     */
    public function btnAdd($label = '添加', $class = 'btn-primary')
    {
        $this->linkBtn('tool-add', $label)->icon('mdi-plus')->class($class);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param string $class
     * @return $this
     */
    public function btnDelete($label = '删除', $class = 'btn-danger')
    {
        $this->linkBtn('tool-delete', $label)->icon('mdi-delete')->class($class)->postChecked(url('delete'));
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param string $class
     * @return $this
     */
    public function btnDisable($label = '禁用', $class = 'btn-warning')
    {
        $this->linkBtn('tool-disable', $label)->icon('mdi-block-helper')->class($class)->postChecked(url('disable'));
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param string $class
     * @return $this
     */
    public function btnEnable($label = '启用', $class = 'btn-success')
    {
        $this->linkBtn('tool-enable', $label)->icon('mdi-check')->class($class)->postChecked(url('enable'));
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param string $class
     * @return $this
     */
    public function btnRefresh($label = '', $class = 'btn-default')
    {
        $this->linkBtn('bar-refresh', $label)->icon('mdi-refresh')->class($class)->attr('title="刷新"');
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
    public function postChecked($url, $label = '', $class = 'btn-success', $icon = 'mdi-checkbox-marked-outline', $confirm = true, $attr = '')
    {
        $action = preg_replace('/.+?\/(\w+)\.?\w+$/', '$1', $url, -1, $count);

        if (!$count) {
            $action = mt_rand(10, 99);
        }

        $this->linkBtn('bar-' . $action, $label)->postChecked($url, $confirm)->icon($icon)->class($class)->attr($attr);

        return $this;
    }
}
