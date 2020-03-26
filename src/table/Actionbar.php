<?php

namespace tpext\builder\table;

use tpext\builder\common\Toolbar;

class Actionbar extends Toolbar
{
    protected $pk;

    protected $extKey = '';

    protected $rowid;

    protected $rowdata;

    protected $script = [];

    protected $useLayer = true;

    protected $mapClass = [];

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
     * @param string $val
     * @return $this
     */
    public function extKey($val)
    {
        $this->extKey = $val;
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

            if ($this->extKey) {
                $elm->extKey($this->extKey);
            }

            if ($this->rowid) {
                $elm->dataid($this->rowid);
            }

            if ($this->rowdata) {
                $elm->parse($this->rowdata);
            }

            if ($this->mapClass) {
                $elm->mapClass($this->mapClass);
            }

            $elm->useLayer($this->useLayer);
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
     * @param array $data
     * @return $this
     */
    public function rowdata($data)
    {
        if (isset($data[$this->pk])) {
            $this->rowid = $data[$this->pk];
        }

        $this->rowdata = $data;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $data
     * @return $this
     */
    public function mapClass($data)
    {
        $this->mapClass = $data;
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
     * @param string $url
     * @param string $label
     * @param string $class
     * @param string $icon
     * @param string $attr
     * @return $this
     */
    public function btnEdit($url = '', $label = '', $class = 'btn-primary', $icon = 'mdi-lead-pencil', $attr = 'title="编辑"')
    {
        if (empty($url)) {
            $url = url('edit', ['id' => '__data.pk__']);
        }
        $this->actionBtn('edit', $label)->href($url)->icon($icon)->addClass($class)->attr($attr);
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
    public function btnView($url = '', $label = '', $class = 'btn-primary', $icon = 'mdi-lead-pencil', $attr = 'title="查看"')
    {
        if (empty($url)) {
            $url = url('view', ['id' => '__data.pk__']);
        }
        $this->actionBtn('edit', $label)->href($url)->icon($icon)->addClass($class)->attr($attr);
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
    public function btnDelete($postUrl = '', $label = '', $class = 'btn-danger', $icon = 'mdi-delete', $confirm = true, $attr = 'title="删除"')
    {
        if (empty($postUrl)) {
            $postUrl = url('delete');
        }
        $this->actionBtn('delete', $label)->postRowid($postUrl, $confirm)->icon($icon)->addClass($class)->attr($attr);
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
    public function btnDisable($postUrl = '', $label = '', $class = 'btn-warning', $icon = 'mdi-block-helper', $confirm = true, $attr = 'title="禁用"')
    {
        if (empty($postUrl)) {
            $postUrl = url('disable');
        }
        $this->actionBtn('disable', $label)->postRowid($postUrl, $confirm)->icon($icon)->addClass($class)->attr($attr);
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
    public function btnEnable($postUrl = '', $label = '', $class = 'btn-success', $icon = 'mdi-check', $confirm = true, $attr = 'title="启用"')
    {
        if (empty($postUrl)) {
            $postUrl = url('enable');
        }
        $this->actionBtn('enable', $label)->postRowid($postUrl, $confirm)->icon($icon)->addClass($class)->attr($attr);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $name
     * @param string $url
     * @param string $label
     * @param string $class
     * @param string $icon
     * @param string $attr
     * @return $this
     */
    public function btnLink($name = '', $url, $label = '', $class = 'btn-secondary', $icon = '', $attr = '')
    {
        if (!$name) {
            $name = preg_replace('/.+?\/(\w+)\.?\w+$/', '$1', $url, -1, $count);

            if (!$count) {
                $name = mt_rand(10, 99);
            }
        }

        $this->actionBtn($name, $label)->href($url)->icon($icon)->addClass($class)->attr($attr);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $name
     * @param string $postUrl
     * @param string $label
     * @param string $class
     * @param string $icon
     * @param string $attr
     * @param boolean $confirm
     * @return $this
     * 
     */
    public function btnPostRowid($name = '', $postUrl, $label = '', $class = 'btn-secondary', $icon = 'mdi-checkbox-marked-outline', $attr = '', $confirm = true)
    {
        if (!$name) {
            $name = preg_replace('/.+?\/(\w+)\.?\w+$/', '$1', $postUrl, -1, $count);

            if (!$count) {
                $name = mt_rand(10, 99);
            }
        }

        $this->actionBtn($name, $label)->postRowid($postUrl, $confirm)->icon($icon)->addClass($class)->attr($attr);

        return $this;
    }
}
