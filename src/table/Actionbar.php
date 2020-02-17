<?php

namespace tpext\builder\table;

use tpext\builder\common\Toolbar;

class Actionbar extends Toolbar
{
    protected $pk;

    protected $rowid;

    protected $script = [];

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
     * @param string $val
     * @return $this
     */
    public function pk($val)
    {
        $this->pk = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string|int $val
     * @return $this
     */
    public function rowid($val)
    {
        $this->rowid = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function buttons()
    {
        $this->btnEdit();
        $this->btnDelete();

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $label
     * @param string $class
     * @return $this
     */
    public function btnEdit($label = '编辑', $class = 'btn-primary')
    {
        $this->linkBtn('add', $label)->icon('mdi-lead-pencil')->class($class);
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
        $this->linkBtn('action-delete', $label)->icon('mdi-delete')->class($class)->postRowid(url('delete'));
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
        $this->linkBtn('action-disable', $label)->icon('mdi-block-helper')->class($class)->postRowid(url('disable'));
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
        $this->linkBtn('action-enable', $label)->icon('mdi-check')->class($class)->postRowid(url('enable'));
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
    public function postRowid($url, $label = '', $class = 'btn-success', $icon = 'mdi-checkbox-marked-outline', $confirm = true, $attr = '')
    {
        $action = preg_replace('/.+?\/(\w+)\.?\w+$/', '$1', $url, -1, $count);

        if (!$count) {
            $action = mt_rand(10, 99);
        }

        $this->linkBtn('action-' . $action, $label)->postRowid($url, $confirm)->icon($icon)->class($class)->attr($attr);

        return $this;
    }
}
