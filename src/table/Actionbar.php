<?php

namespace tpext\builder\table;

use tpext\builder\common\Toolbar;

class Actionbar extends Toolbar
{
    protected $pk;

    protected $rowId;

    protected $rowData;

    protected $mapClass = [];

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @param array|string $size
     * @return $this
     */
    public function useLayerAll($val, $size = [])
    {
        foreach ($this->elms as $elm) {
            $elm->useLayer($val, $size);
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $barType
     * @return $this
     */
    public function created($barType = '')
    {
        parent::created();
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

            if ($this->rowId) {
                $elm->dataId($this->rowId);
            }

            if ($this->rowData) {
                $elm->parseUrl($this->rowData);
            }

            if ($this->mapClass) {
                $elm->mapClass($this->mapClass);
            }
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
    public function rowData($data)
    {
        if (isset($data[$this->pk])) {
            $this->rowId = $data[$this->pk];
        }

        $this->rowData = $data;

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
            $url = url('edit', ['id' => '__data.pk__'])->__toString();
        }
        if ($attr == 'title="编辑"') {
            $attr = 'title="' . __blang('bilder_action_edit') . '"';
        }
        $this->actionBtn('edit', $label)->href($url)->icon($icon)->addClass($class)->addAttr($attr);
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
    public function btnView($url = '', $label = '', $class = 'btn-info', $icon = 'mdi-eye-outline', $attr = 'title="查看"')
    {
        if (empty($url)) {
            $url = url('view', ['id' => '__data.pk__'])->__toString();
        }
        if ($attr == 'title="查看"') {
            $attr = 'title="' . __blang('bilder_action_view') . '"';
        }
        $this->actionBtn('view', $label)->href($url)->icon($icon)->addClass($class)->addAttr($attr);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $postUrl
     * @param string $label
     * @param string $class
     * @param string $icon
     * @param string $attr
     * @param boolean|string $confirm
     * @return $this
     */
    public function btnDelete($postUrl = '', $label = '', $class = 'btn-danger', $icon = 'mdi-delete', $attr = 'title="删除"', $confirm = true)
    {
        if (empty($postUrl)) {
            $postUrl = url('delete')->__toString();
        }
        if ($attr == 'title="删除"') {
            $attr = 'title="' . __blang('bilder_action_delete') . '"';
        }
        $this->actionBtn('delete', $label)->postRowid($postUrl, $confirm)->icon($icon)->addClass($class)->addAttr($attr);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $postUrl
     * @param string $label
     * @param string $class
     * @param string $icon
     * @param string $attr
     * @param boolean|string $confirm
     * @return $this
     */
    public function btnDisable($postUrl = '', $label = '', $class = 'btn-warning', $icon = 'mdi-block-helper', $attr = 'title="禁用"', $confirm = true)
    {
        if (empty($postUrl)) {
            $postUrl = url('enable', ['state' => 0])->__toString();
        }
        if ($attr == 'title="禁用"') {
            $attr = 'title="' . __blang('bilder_action_disable') . '"';
        }
        $this->actionBtn('disable', $label)->postRowid($postUrl, $confirm)->icon($icon)->addClass($class)->addAttr($attr);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $postUrl
     * @param string $label
     * @param string $class
     * @param string $icon
     * @param string $attr
     * @param boolean|string $confirm
     * @return $this
     */
    public function btnEnable($postUrl = '', $label = '', $class = 'btn-success', $icon = 'mdi-check', $attr = 'title="启用"', $confirm = true)
    {
        if (empty($postUrl)) {
            $postUrl = url('enable', ['state' => 1])->__toString();
        }
        if ($attr == 'title="启用"') {
            $attr = 'title="' . __blang('bilder_action_enable') . '"';
        }
        $this->actionBtn('enable', $label)->postRowid($postUrl, $confirm)->icon($icon)->addClass($class)->addAttr($attr);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $enableTitle
     * @param string $disableTitle
     * @return $this
     */
    public function btnEnableAndDisable($enableTitle = '启用', $disableTitle = '禁用')
    {
        if ($enableTitle == '启用') {
            $enableTitle = __blang('bilder_action_enable');
        }
        if ($enableTitle == '禁用') {
            $enableTitle = __blang('bilder_action_disable');
        }
        $this->btnEnable()->getCurrent()->attr('title="' . $enableTitle . '"');
        $this->btnDisable()->getCurrent()->attr('title="' . $disableTitle . '"');

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
    public function btnLink($name = '', $url = '', $label = '', $class = 'btn-secondary', $icon = '', $attr = '')
    {
        if (!$name) {
            $name = preg_replace('/.+?\/(\w+)(\.\w+)?$/', '$1', $url, -1, $count);

            if (!$count) {
                $name = preg_replace('/\W/', '_', $url);
            }
        }

        $this->actionBtn($name, $label)->href($url)->icon($icon)->addClass($class)->addAttr($attr);

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
     * @param boolean|string $confirm
     * @return $this
     *
     */
    public function btnPostRowid($name = '', $postUrl = '', $label = '', $class = 'btn-secondary', $icon = 'mdi-checkbox-marked-outline', $attr = '', $confirm = true)
    {
        if (!$name) {
            $name = preg_replace('/.+?\/(\w+)(\.\w+)?$/', '$1', $postUrl, -1, $count);

            if (!$count) {
                $name = preg_replace('/\W/', '_', $postUrl);
            }
        }

        $this->actionBtn($name, $label)->postRowid($postUrl, $confirm)->icon($icon)->addClass($class)->addAttr($attr);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $items
     * @param string $label
     * @param string $class
     * @param string $icon
     * @param string $attr
     * @return $this
     */
    public function btnActions($items, $label = '操作', $class = 'btn-secondary', $icon = '', $attr = '')
    {
        if ($label == '操作') {
            $label = __blang('bilder_action_operation');
        }
        $this->actions('actions', $label)->items($items)->addClass($class)->icon($icon)->addAttr($attr);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function html($val)
    {
        parent::html($val);
        return $this;
    }

    /**
     * Undocumented function
     * 换行
     * @return $this
     */
    public function br()
    {
        parent::html('<br />');
        return $this;
    }
}
