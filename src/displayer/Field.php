<?php

namespace tpext\builder\displayer;

use think\Model;
use think\response\View as ViewShow;
use tpext\builder\common\Builder;
use tpext\builder\common\Module;
use tpext\builder\common\Renderable;
use tpext\builder\form\Wapper;

class Field implements Renderable
{
    protected $tableRowKey = '';

    protected $name = '';

    protected $label = '';

    protected $js = [];

    protected $css = [];

    protected $script = [];

    protected $style = '';

    protected $view = 'field';

    protected $value = '';

    protected $default = '';

    protected $icon = '';

    protected $rules = '';

    protected $autoPost = '';

    protected $autoPostRefresh = false;

    protected $showLabel = true;

    protected $class = '';

    protected $labelClass = '';

    protected $attr = '';

    protected $labelArrt = '';

    protected $errorClass = '';

    protected $error = '';

    protected $size = [2, 8];

    protected $help = '';

    protected $readonly = '';

    protected $disabled = '';

    protected $wapper = null;

    protected $useDefauleFieldClass = true;

    protected static $helptempl;

    protected static $labeltempl;

    protected $mapClassWhen = [];

    protected $required = false;

    public function __construct($name, $label = '')
    {
        if (empty($label)) {
            $label = ucfirst($name);
        }

        $this->name = $name;
        $this->label = $label;
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
     * @return string
     */
    public function getId()
    {
        return 'form-' . preg_replace('/\W/', '', $this->name . $this->tableRowKey);
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getName()
    {
        return $this->name . $this->tableRowKey;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function tableRowKey($val)
    {
        $this->tableRowKey = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @param boolean $refresh
     * @return $this
     */
    public function autoPost($url = '', $refresh = true)
    {
        if (empty($url)) {
            $url = url('autoPost');
        }
        $this->autoPost = $url;
        $this->autoPostRefresh = $refresh;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function value($val)
    {
        if (is_array($val)) {
            $val = implode(',', $val);
        }
        $this->value = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string|int|mixed $val
     * @return $this
     */
    function default($val = '') {
        $this->default = $val;
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
    public function rules($val)
    {
        $this->rules = $val;
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
    public function style($val)
    {
        $this->style = $val;
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
    public function addClass($val)
    {
        $this->class .= ' ' . $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function addAttr($val)
    {
        $this->attr .= ' ' . $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function addStyle($val)
    {
        $this->attr .= $val;
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
    public function size($label = 2, $element = 8)
    {
        $this->size = [$label, $element];
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
     * @return $this
     */
    public function readonly($val = true)
    {
        $this->readonly = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function disabled($val = true)
    {
        $this->disabled = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function required($val = true)
    {
        $this->required = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function showLabel($val)
    {
        $this->showLabel = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function useDefauleFieldClass($val)
    {
        $this->useDefauleFieldClass = $val;
        return $this;
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
     * @return string
     */
    protected function getValue()
    {
        return $this->value;
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
     * @return array
     */
    public function getScript()
    {
        return $this->script;
    }

    /**
     * Undocumented function
     *
     * @param integer $labelMin
     * @return $this
     */
    public function fullSize($labelMin = 3)
    {
        $this->size = [$labelMin, 12 - $labelMin];
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|Model $data
     * @return $this
     */
    public function fill($data = [])
    {
        if (!empty($this->name) && isset($data[$this->name])) {
            $value = $data[$this->name];

            if (is_array($value)) {
                $value = implode(',', $value);
            }

            $this->value = $value;
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|string $value acses
     * @param string $class
     * @return $this
     */
    public function mapClassWhen($values, $class)
    {
        if (!is_array($values)) {
            $values = [$values];
        }
        $this->mapClassWhen = [$values, $class];
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return mixed
     */
    public function render()
    {
        $vars = $this->commonVars();

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }

    protected function getViewInstance()
    {
        $template = Module::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'displayer', $this->view . '.html']);

        $viewshow = new ViewShow($template);

        return $viewshow;
    }

    protected function autoPostScript()
    {
        $class = 'row-' . $this->name;

        $refresh = $this->autoPostRefresh ? 1 : 0;

        $script = <<<EOT

        tpextbuilder.autoPost('{$class}', '{$this->autoPost}' ,{$refresh});

EOT;
        $this->script[] = $script;

        return $script;
    }

    public function beforRender()
    {
        if (!empty($this->js)) {
            Builder::getInstance()->addJs($this->js);
        }

        if (!empty($this->css)) {
            Builder::getInstance()->addCss($this->css);
        }

        if ($this->autoPost) {
            $this->autoPostScript();
        }

        if (!empty($this->script)) {
            Builder::getInstance()->addScript($this->script);
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function commonVars()
    {
        if (empty(static::$helptempl)) {
            static::$helptempl = Module::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'displayer', 'helptempl.html']);
        }

        if (empty(static::$labeltempl)) {
            static::$labeltempl = Module::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'displayer', 'labeltempl.html']);
        }

        if (is_array($this->default)) {
            $this->default = implode(',', $this->default);
        }

        $value = !($this->value === '' || $this->value === null) ? $this->value : $this->default;

        $mapClass = '';

        if (!empty($this->mapClassWhen)) {
            if (in_array($value, $this->mapClassWhen[0])) {
                $mapClass = ' ' . $this->mapClassWhen[1];
            }
        }

        $vars = [
            'id' => $this->getId(),
            'label' => $this->label,
            'name' => $this->getName(),
            'requiredStyle' => $this->required ? '' : 'style="visibility: hidden;"',
            'tableRowKey' => $this->tableRowKey,
            'value' => $value,
            'class' => ' ' . $this->class . $mapClass,
            'attr' => $this->attr . ($this->disabled ? ' disabled' : '') . ($this->readonly ? ' readonly onclick="return false;"' : '') . (empty($this->style) ? '' : ' style="' . $this->style . '"'),
            'error' => $this->error,
            'size' => $this->size,
            'labelClass' => $this->size[0] < 12 ? $this->labelClass . ' control-label text-right' : $this->labelClass,
            'labelAttr' => empty($this->labelAttr) ? '' : ' ' . $this->labelAttr,
            'help' => $this->help,
            'showLabel' => $this->showLabel,
            'helptempl' => static::$helptempl,
            'labeltempl' => static::$labeltempl,
        ];

        return $vars;
    }
}
