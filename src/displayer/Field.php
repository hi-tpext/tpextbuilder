<?php

namespace tpext\builder\displayer;

use think\Model;
use think\response\View as ViewShow;
use tpext\builder\common\Builder;
use tpext\builder\common\Module;
use tpext\builder\common\Wapper;
use tpext\builder\form\Fillable;
use tpext\builder\traits\HasDom;

class Field implements Fillable
{
    use HasDom;

    protected $extKey = '';

    protected $extNameKey = '';

    protected $name = '';

    protected $label = '';

    protected $js = [];

    protected $css = [];

    protected $stylesheet = '';

    protected $script = [];

    protected $view = 'field';

    protected $value = '';

    protected $default = '';

    protected $icon = '';

    protected $autoPost = '';

    protected $autoPostRefresh = false;

    protected $showLabel = true;

    protected $labelClass = '';

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

    protected $mapClassValue = '';

    protected $required = false;

    protected $minify = true;

    protected $arrayName = false;

    protected $rand = '';

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
        return 'form-' . preg_replace('/\W/', '', $this->name) . $this->extKey;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getName()
    {
        if ($this->arrayName) {
            return $this->arrayName[0] . $this->name . $this->arrayName[1];
        }

        return $this->name . $this->extNameKey;
    }

    /**
     * Undocumented function
     *
     * @param array $val
     * @return $this
     */
    public function arrayName($val)
    {
        $this->arrayName = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
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
     * @param string $val
     * @return $this
     */
    public function extNameKey($val)
    {
        $this->extNameKey = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @param boolean $refresh
     * @return $this
     */
    public function autoPost($url = '', $refresh = false)
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
     * @return boolean
     */
    public function canMinify()
    {
        return $this->minify;
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
     * @param \tpext\builder\form\FRow|\tpext\builder\search\SRow|\tpext\builder\table\TColumn $wapper
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
     * @return \tpext\builder\form\FRow|\tpext\builder\search\SRow|\tpext\builder\table\TColumn
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
     * @return $this
     */
    public function clearScript()
    {
        $this->script = [];
        return $this;
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

        if (!empty($this->mapClassWhen)) {
            $field = $this->mapClassWhen[2];
            if ($field != $this->name && isset($data[$field])) {
                $this->mapClassValue = $data[$field];
            }
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $event
     * @return $this
     */
    public function trigger($event)
    {
        $fieldId = $this->getId();

        $script = <<<EOT

        $('#{$fieldId}').trigger('{$event}');

EOT;
        $this->script[] = $script;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $script
     * @return $this
     */
    public function addScript($script)
    {
        $this->script[] = $script;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|string $values
     * @param string $class
     * @param string $field
     * @return $this
     */
    public function mapClassWhen($values, $class, $field = '')
    {
        if (empty($field)) {
            $field = $this->name;
        }

        if (!is_array($values)) {
            $values = [$values];
        }
        $this->mapClassWhen = [$values, $class, $field];
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

    public function __toString()
    {
        return $this->render();
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
            if ($this->minify) {
                Builder::getInstance()->addJs($this->js);
            } else {
                Builder::getInstance()->customJs($this->js);
            }
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

        if (!empty($this->stylesheet)) {
            Builder::getInstance()->addStyleSheet($this->stylesheet);
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function renderValue()
    {
        if (is_array($this->default)) {
            $this->default = implode(',', $this->default);
        } else if ($this->default === true) {
            $this->default = 1;
        } else if ($this->default === false) {
            $this->default = 0;
        }

        if ($this->value === true) {
            $this->value = 1;
        } else if ($this->value === false) {
            $this->value = 0;
        }

        $value = !($this->value === '' || $this->value === null) ? $this->value : $this->default;

        return $value;
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

        $value = $this->renderValue();

        $mapClass = '';

        if (!empty($this->mapClassWhen)) {
            if ($this->mapClassWhen[2] == $this->name) {
                $this->mapClassValue = $value;
            }

            if (in_array($this->mapClassValue, $this->mapClassWhen[0])) {
                $mapClass = ' ' . $this->mapClassWhen[1];
            }
        }
        $extendAttr = ($this->isRequired() ? ' required="true"' : '') . ($this->disabled ? ' disabled' : '') . ($this->readonly ? ' readonly onclick="return false;"' : '');

        $vars = [
            'id' => $this->getId(),
            'label' => $this->label,
            'name' => $this->getName(),
            'requiredStyle' => $this->required ? '' : 'style="visibility: hidden;"',
            'extKey' => $this->extKey,
            'extNameKey' => $this->extNameKey,
            'value' => $value,
            'class' => ' ' . $this->getClass() . $mapClass,
            'attr' => $this->getAttrWithStyle() . $extendAttr,
            'error' => $this->error,
            'size' => $this->size,
            'labelClass' => $this->size[0] < 12 ? $this->labelClass . ' control-label text-right' : $this->labelClass . ' full-label',
            'labelAttr' => empty($this->labelAttr) ? '' : ' ' . $this->labelAttr,
            'help' => $this->help,
            'showLabel' => $this->showLabel,
            'helptempl' => static::$helptempl,
            'labeltempl' => static::$labeltempl,
        ];

        return $vars;
    }
}
