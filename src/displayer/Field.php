<?php

namespace tpext\builder\displayer;

use think\response\View as ViewShow;
use tpext\builder\common\Builder;
use tpext\builder\common\Plugin;
use tpext\builder\form\Wapper;

class Field
{
    protected $name = '';

    protected $label = '';

    protected $options = [];

    protected $js = [];

    protected $css = [];

    protected $view = 'field';

    protected $value = '';

    protected $icon = '';

    protected $rules = '';

    protected $editable = true;

    protected $showLabel = true;

    protected $class = '';

    protected $labelClass = '';

    protected $attr = '';

    protected $labelArrt = '';

    protected $errorClass = '';

    protected $error = '';

    protected $size = [2, 9];

    protected $help = '';

    protected $readonly = '';

    protected $disabled = '';

    protected $wapper = null;

    protected $useDefauleFieldClass = true;

    protected static $helptempl;

    protected static $labeltempl;

    public function __construct($name, $label = '')
    {
        if (empty($label)) {
            $label = ucfirst($name);
        }

        $this->name = $name;
        $this->label = $label;

        $this->publishAssets();
    }

    public function created()
    {
        $fieldType = preg_replace('/.+?\\\(\w+)$/', '$1', get_called_class());

        $fieldType = lcfirst($fieldType);

        $defaultClass = Wapper::hasDefaultFieldClass($fieldType);

        if (!empty($defaultClass)) {
            $this->class = $defaultClass;
        }
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function value($val)
    {
        $this->value = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function name($val)
    {
        $this->name = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function label($val)
    {
        $this->label = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    function class ($val)
    {
        $this->class = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function labelClass($val)
    {
        $this->labelClass = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function attr($val)
    {
        $this->attr = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function labelAttr($val)
    {
        $this->labelAttr = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $val
     * @return $this
     */
    public function size($val)
    {
        $this->size = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function help($val)
    {
        $this->help = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function error($val)
    {
        $this->error = $val;
        $this->errorClass($val ? 'has-error' : '');
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function errorClass($val)
    {
        $this->errorClass = $val;
        $this->wapper->errorClass($val);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return void
     */
    public function readonly($val = true)
    {
        $this->readonly = $val;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return void
     */
    public function disabled($val = true)
    {
        $this->disabled = $val;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return void
     */
    protected function showLabel($val)
    {
        $this->showLabel = $val;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return void
     */
    public function useDefauleFieldClass($val)
    {
        $this->useDefauleFieldClass = $val;
    }

    /**
     * Undocumented function
     *
     * @param \tpext\builder\form\Row $wapper
     * @return $this
     */
    public function setWapper($wapper)
    {
        $this->wapper = $wapper;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return \tpext\builder\form\Row
     */
    public function getWapper()
    {
        return $this->wapper;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getAttr()
    {
        return $this->attr;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getLabelClass()
    {
        return $this->labelClass;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getLabelAttr()
    {
        return $this->labelArrt;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Undocumented function
     *
     * @param boolean $editable
     * @return $this
     */
    public function editable($editable = true)
    {
        $this->editable = $editable;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isEditable()
    {
        return $this->editable;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getJs()
    {
        return $this->js;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getCss()
    {
        return $this->css;
    }

    /**
     * Undocumented function
     *
     * @return mixed
     */
    public function render()
    {
        $vars = $this->commonVars();

        $config = [];

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->config($config)->getContent();
    }

    protected function getViewInstance()
    {
        $template = Plugin::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'displayer', $this->view . '.html']);

        $viewshow = new ViewShow($template);

        return $viewshow;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function publishAssets()
    {
        if (!empty($this->js)) {
            Builder::getInstance()->addJs($this->js);
        }

        if (!empty($this->css)) {
            Builder::getInstance()->addCss($this->css);
        }
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function commonVars()
    {
        if (empty(static::$helptempl)) {
            static::$helptempl = Plugin::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'displayer', 'helptempl.html']);
        }

        if (empty(static::$labeltempl)) {
            static::$labeltempl = Plugin::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'displayer', 'labeltempl.html']);
        }

        $vars = [
            'label' => $this->label,
            'name' => $this->name,
            'value' => $this->value,
            'class' => ' ' . $this->class,
            'attr' => $this->attr . ($this->disabled ? ' disabled' : '') . ($this->readonly ? ' readonly onclick="return false;"' : ''),
            'error' => $this->error,
            'size' => $this->size,
            'labelClass' => $this->size[0] < 12 ? $this->labelClass . ' control-label text-right' : $this->labelClass,
            'labelAttr' => empty($this->labelAttr) ? '' : ' ' . $this->labelAttr,
            'options' => $this->options,
            'help' => $this->help,
            'error' => $this->error,
            'showLabel' => $this->showLabel,
            'helptempl' => static::$helptempl,
            'labeltempl' => static::$labeltempl,
        ];

        return $vars;
    }
}
