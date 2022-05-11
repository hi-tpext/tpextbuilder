<?php

namespace tpext\builder\displayer;

use think\Model;
use tpext\builder\common\Builder;
use tpext\builder\common\Module;
use tpext\builder\common\Wrapper;
use tpext\builder\form\Fillable;
use tpext\builder\traits\HasDom;
use tpext\common\ExtLoader;

/**
 * Field class
 */
class Field implements Fillable
{
    use HasDom;

    protected $extKey = '';

    protected $extNameKey = '';

    protected $name = '';

    protected $innerName = '';

    protected $label = '';

    protected $js = [];

    protected $css = [];

    protected $stylesheet = '';

    protected $script = [];

    protected $view = 'field';

    /**
     * @var string|array
     */
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

    protected $wrapper = null;

    protected static $helptempl;

    protected static $labeltempl;

    protected $mapClass = [];

    protected $required = false;

    protected $minify = true;

    protected $arrayName = false;

    protected $to = '';

    protected $data = [];

    /**
     * Undocumented variable
     *
     * @var \Closure
     */
    protected $rendering = null;

    public function __construct($name, $label = '')
    {
        $this->name = trim($name);

        if (empty($label) && !empty($this->name)) {
            $label = lang(ucfirst($this->name));
        }

        if (strstr($this->name, '.')) {
            $arr = explode('.', $this->name);
            $this->arrayName([$arr[0] . '[', ']']);
            $this->innerName = $arr[1];
            $this->extKey = '-' . $arr[0];
        }

        $this->label = $label;
    }

    /**
     * Undocumented function
     *
     * @param string $fieldType
     * @return $this
     */
    public function created($fieldType = '')
    {
        $fieldType = $fieldType ? $fieldType : get_called_class();

        $fieldType = lcfirst($fieldType);

        $defaultClass = Wrapper::hasDefaultFieldClass($fieldType);

        if (!empty($defaultClass)) {
            $this->class = $defaultClass;
        }

        ExtLoader::trigger('tpext_displayer_created', $this);

        return $this;
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

            if ($this->innerName) {
                return $this->arrayName[0] . $this->innerName . $this->arrayName[1];
            }

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
            $url = url('autopost');
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
     * @param string|\Closure $val
     * @return $this
     */
    public function to($val)
    {
        $this->to = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string|int|mixed $val
     * @return $this
     */
    public function default($val = '')
    {
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
     * @param integer $label
     * @param integer $element
     * @return $this
     */
    public function size($label = 2, $element = 8)
    {
        $this->size = [$label, $element];
        return $this;
    }

    /**
     * Undocumented function
     * @example 1 [int] 4 => class="col-md-4"
     * @example 2 [string] '4 xls-4' => class="col-md-4 xls-4"
     *
     * @param int|string $val
     * @return $this
     */
    public function cloSize($val)
    {
        $this->getWrapper()->cloSize($val);
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
        $this->wrapper->errorClass($val);
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
    public function isShowLabel()
    {
        return $this->showLabel;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getSize()
    {
        return $this->size;
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
     * @param array|string $val
     * @return $this
     */
    public function addJs($val)
    {
        if (!is_array($val)) {
            $val = [$val];
        }
        $this->js = array_merge($this->js, $val);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|string $val
     * @return $this
     */
    public function removeJs($val)
    {
        if (!is_array($val)) {
            $val = [$val];
        }

        foreach ($this->js as $k => $j) {
            if (in_array($j, $val)) {
                unset($this->js[$k]);
            }
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|string $val
     * @return $this
     */
    public function removeCss($val)
    {
        if (!is_array($val)) {
            $val = [$val];
        }

        foreach ($this->css as $k => $c) {
            if (in_array($c, $val)) {
                unset($this->css[$k]);
            }
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @param string $newVal
     * @return $this
     */
    public function replaceJs($val, $newVal)
    {
        foreach ($this->js as $k => $j) {
            if ($val == $j) {
                $this->js[$k] = $newVal;
                break;
            }
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @param string $newVal
     * @return $this
     */
    public function replaceCss($val, $newVal)
    {
        foreach ($this->css as $k => $c) {
            if ($val == $c) {
                $this->css[$k] = $newVal;
                break;
            }
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|string $val
     * @return $this
     */
    public function addCss($val)
    {
        if (!is_array($val)) {
            $val = [$val];
        }
        $this->css = array_merge($this->css, $val);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param \tpext\builder\form\FRow|\tpext\builder\search\SRow|\tpext\builder\table\TColumn $wrapper
     * @return $this
     */
    public function setWrapper($wrapper)
    {
        $this->wrapper = $wrapper;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return \tpext\builder\form\FRow|\tpext\builder\search\SRow|\tpext\builder\table\TColumn
     */
    public function getWrapper()
    {
        return $this->wrapper;
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
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Undocumented function
     *
     * @return string|array
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getHelp()
    {
        return $this->help;
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
        if (empty($this->size) || (is_numeric($this->size[0]) && is_numeric($this->size[1]))) {

            $this->size = [$labelMin, 12 - $labelMin];
        }

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
        if (!empty($this->name)) {

            $hasVal = false;
            $value = '';
            if (strstr($this->name, '.')) {

                $arr = explode('.', $this->name);

                if (isset($data[$arr[0]])) {

                    if (isset($data[$arr[0]][$arr[1]])) {
                        $value = $data[$arr[0]][$arr[1]];
                        $hasVal = true;
                    }
                } else { //尝试读取上一层级的值

                    if (isset($data[$arr[1]])) {
                        $value = $data[$arr[1]];
                        $hasVal = true;
                    }
                }
            } else if (isset($data[$this->name])) {

                $value = $data[$this->name];
                $hasVal = true;
            }

            if (is_array($value)) {
                $value = implode(',', $value);
            }

            if ($hasVal) {
                $this->value = $value;
            }
        }

        $this->data = $data;

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
     * @param array|string|int|\Closure $values
     * @param string $class
     * @param string $field default current field
     * @param string $logic in_array|not_in_array|eq|gt|lt|egt|elt|strstr|not_strstr
     * @return $this
     */
    public function mapClass($values, $class, $field = '', $logic = 'in_array')
    {
        if (empty($field)) {
            $field = $this->name;
        }

        if (!($values instanceof \Closure) && !is_array($values)) {
            $values = [$values];
        }

        $this->mapClass[] = [$values, $class, $field, $logic];
        return $this;
    }

    /**
     * 弃用，使用mapClass代替
     * @deprecated 1.8.93
     *
     * @param array|string|int|\Closure $values
     * @param string $class
     * @param string $field
     * @param string $logic
     * @return $this
     */
    public function mapClassWhen($values, $class, $field = '', $logic = 'in_array')
    {
        return $this->mapClass($values, $class, $field, $logic);
    }

    /**
     * Undocumented function
     *
     * @param array $groupArr
     * @example location1 [[$values1, $class1, $field1, $logic1], [$values2, $class2, $field2, $logic2], ... ]
     * @example location2 ['class1' => [$values1, $field1, $logic1], 'class2'=> [$values2, $field2, $logic2], ... ]
     * @example location3 ['class1' => function closure1(){...}, 'class2'=> function closure2(){...}, ... ]
     * @return $this
     */
    public function mapClassGroup($groupArr)
    {
        foreach ($groupArr as $key => $g) {
            if (is_int($key)) { //  1
                $values = $g[0];
                $class = $g[1];
                $field = isset($g[2]) ? $g[2] : '';
                $logic = isset($g[3]) ? $g[3] : '';
                $this->mapClass($values, $class, $field, $logic);
            } else if (is_string($key)) { //  2 /  3
                if (is_array($g)) //2
                {
                    $values = $g[0];
                    $field = isset($g[1]) ? $g[1] : '';
                    $logic = isset($g[2]) ? $g[2] : '';
                    $this->mapClass($values, $key, $field, $logic);
                } else if ($g instanceof \Closure) {
                    $this->mapClass($g, $key);
                }
            }
        }

        return $this;
    }

    /**
     * 弃用，使用mapClassGroup代替
     *
     * @deprecated 1.8.93
     * @param array $groupArr
     * @return $this
     */
    public function mapClassWhenGroup($groupArr)
    {
        return $this->mapClassGroup($groupArr);
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function getCsrfToken()
    {
        return Builder::getInstance()->getCsrfToken();
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
        $template = Module::getInstance()->getViewsPath() . 'displayer' . DIRECTORY_SEPARATOR . $this->view . '.html';

        $viewshow = view($template);

        return $viewshow;
    }

    protected function autoPostScript()
    {
        $class = 'row-' . $this->name . '-td';

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

            if (Builder::checkUrl($this->autoPost)) {
                $this->autoPostScript();
            } else {
                $this->readonly();
            }
        }

        if (!empty($this->script)) {
            Builder::getInstance()->addScript($this->script);
        }

        if (!empty($this->stylesheet)) {
            Builder::getInstance()->addStyleSheet($this->stylesheet);
        }

        if ($this->rendering && $this->rendering instanceof \Closure) {
            $this->rendering->call($this, $this);
        }

        ExtLoader::trigger('tpext_displayer_befor_render', $this);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function renderValue()
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

        if (!empty($this->to)) {
            $value = $this->parseToValue($value);
        }

        return $value;
    }

    protected function parseToValue($value)
    {
        $data = $this->data;

        $to = $this->to;

        if ($to instanceof \Closure) {
            return $to($value, $data);
        }

        preg_match_all('/\{([\w\.]+)\}/', $this->to, $matches);

        $keys = ['{val}', '{__val__}'];
        $replace = [$value, $value];
        $arr = null;

        foreach ($matches[1] as $match) {
            $arr = explode('.', $match);

            if (count($arr) == 1) {

                $keys[] = '{' . $arr[0] . '}';
                $replace[] = isset($data[$arr[0]]) ? $data[$arr[0]] : '';
            } else if (count($arr) == 2) {

                $keys[] = '{' . $arr[0] . '.' . $arr[1] . '}';
                $replace[] = isset($data[$arr[0]]) && isset($data[$arr[0]][$arr[1]]) ? $data[$arr[0]][$arr[1]] : '-';
            } else {
                //最多支持两层 xx 或 xx.yy
            }
        }

        $val = str_replace($keys, $replace, $to);

        return $val;
    }

    protected function parseMapClass()
    {
        $matchClass = [];
        $values = $class = $field = $logic = $val = $match = null;
        if (!empty($this->mapClass)) {

            foreach ($this->mapClass as $mp) {
                $values = $mp[0];
                $class = $mp[1];
                $field = $mp[2];
                $logic = $mp[3]; //in_array|not_in_array|eq|gt|lt|egt|elt|strstr|not_strstr
                $val = '';
                if (strstr($field, '.')) {

                    $arr = explode('.', $field);

                    if (isset($this->data[$arr[0]]) && isset($this->data[$arr[0]][$arr[1]])) {

                        $val = $this->data[$arr[0]][$arr[1]];
                    } else {
                        continue;
                    }
                } else {

                    if (!isset($this->data[$field])) {
                        continue;
                    }

                    $val = $this->data[$field];
                }


                if ($values instanceof \Closure) {
                    $match = $values($val, $this->data);
                    if ($match) {
                        $matchClass[] = $class;
                    }
                    continue;
                }

                $match = false;
                if ($logic == 'not_in_array' || $logic == '!in_array') {
                    $match = !in_array($val, $values);
                } else if ($logic == 'eq' || $logic == '==') {
                    $match = $val == $values[0];
                } else if ($logic == 'gt' || $logic == '>') {
                    $match = is_numeric($values[0]) && $val > $values[0];
                } else if ($logic == 'lt' || $logic == '<') {
                    $match = is_numeric($values[0]) && $val < $values[0];
                } else if ($logic == 'egt' || $logic == '>=') {
                    $match = is_numeric($values[0]) && $val >= $values[0];
                } else if ($logic == 'elt' || $logic == '<=') {
                    $match = is_numeric($values[0]) && $val <= $values[0];
                } else if ($logic == 'strpos' || $logic == 'strstr') {
                    $match = strstr($val, $values[0]);
                } else if ($logic == 'not_strpos' || $logic == 'not_strstr' || $logic == '!strpos' || $logic == '!strstr') {
                    $match = !strstr($val, $values[0]);
                } else //default in_array
                {
                    $match = in_array($val, $values);
                }
                if ($match) {
                    $matchClass[] = $class;
                }
            }
        }

        if (count($matchClass)) {

            return ' ' . implode(' ', array_unique($matchClass));
        }

        return '';
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function commonVars()
    {
        if (empty(static::$helptempl)) {
            static::$helptempl = Module::getInstance()->getViewsPath() . 'displayer' . DIRECTORY_SEPARATOR . 'helptempl.html';
        }

        if (empty(static::$labeltempl)) {
            static::$labeltempl = Module::getInstance()->getViewsPath() . 'displayer' . DIRECTORY_SEPARATOR . 'labeltempl.html';
        }

        $mapClass = $this->parseMapClass();

        $value = $this->renderValue();

        $extendAttr = ($this->isRequired() ? ' required="true"' : '') . ($this->disabled ? ' disabled' : '') . ($this->readonly ? ' readonly onclick="return false;"' : '');

        $vars = [
            'id' => $this->getId(),
            'label' => $this->label,
            'name' => $this->getName(),
            'requiredStyle' => $this->required ? '' : 'style="display: none;"',
            'extKey' => $this->extKey,
            'extNameKey' => $this->extNameKey,
            'value' => $value,
            'class' => ' row-' . preg_replace('/\W/', '', $this->name) . $this->getClass() . $mapClass,
            'attr' => $this->getAttrWithStyle() . $extendAttr,
            'error' => $this->error,
            'size' => $this->adjustSize(),
            'labelClass' => $this->size[0] < 12 ? $this->labelClass . ' control-label' : $this->labelClass . ' full-label',
            'labelAttr' => empty($this->labelAttr) ? '' : ' ' . $this->labelAttr,
            'help' => $this->help,
            'showLabel' => $this->showLabel,
            'helptempl' => static::$helptempl,
            'labeltempl' => static::$labeltempl,
            'readonly' => $this->readonly,
            'disabled' => $this->disabled,
        ];

        $customVars = $this->customVars();

        if (!empty($customVars)) {
            $vars = array_merge($vars, $customVars);
        }

        return $vars;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function adjustSize()
    {
        $size = $this->size;

        if (is_int($this->size[0])) {
            if ($this->size[0] == 0) {
                $size[0] .= " col-lg-0 col-sm-0 col-xs-0";
            } else if ($this->size[0] <= 3) {
                $size[0] .= " col-lg-{$this->size[0]} col-sm-3 col-xs-12";
            } else if ($this->size[0] <= 4) {
                $size[0] .= " col-lg-{$this->size[0]} col-sm-4 col-xs-12";
            } else {
                $size[0] .= " col-lg-{$this->size[0]} col-sm-12 col-xs-12";
            }
        } else {
            if (!strstr($size[0], 'col-lg-')) {
                $size[0] .= ' col-lg-2';
            }
            if (!strstr($size[0], 'col-sm-')) {
                $size[0] .= ' col-sm-3';
            }
            if (!strstr($size[0], 'col-xs-')) {
                $size[0] .= ' col-xs-12';
            }
        }

        if (is_int($size[1])) {
            if (is_int($this->size[0])) {
                if ($this->size[0] == 0) {
                    $size[0] .= " col-lg-{$this->size[1]} col-sm-12 col-xs-12";
                } else if ($this->size[0] <= 3) {
                    $size[1] .= " col-lg-{$this->size[1]} col-sm-9 col-xs-12";
                } else if ($this->size[0] <= 4) {
                    $size[1] .= " col-lg-{$this->size[1]} col-sm-8 col-xs-12";
                } else {
                    $size[1] .= " col-lg-{$this->size[1]} col-sm-12 col-xs-12";
                }
            } else {
                $size[1] .= " col-lg-{$this->size[1]} col-sm-9 col-xs-12";
            }
        } else {
            if (!strstr($size[1], 'col-lg-')) {
                $size[1] .= ' col-lg-8';
            }
            if (!strstr($size[1], 'col-sm-')) {
                $size[1] .= ' col-sm-9';
            }
            if (!strstr($size[1], 'col-xs-')) {
                $size[1] .= ' col-xs-12';
            }
        }

        return $size;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function customVars()
    {
        return [];
    }

    /**
     * @param \Closure $callback
     * @return $this
     */
    public function rendering($callback)
    {
        $this->rendering = $callback;
        return $this;
    }

    /**
     * 设置table列可排序
     *
     * @param boolean $val
     * @return $this
     */
    public function colSortable($val = true)
    {
        $this->getWrapper()->sortable($val);
        return $this;
    }

    /**
     * 设置table列默认隐藏
     *
     * @param boolean $val
     * @return $this
     */
    public function colHidden($val = true)
    {
        $this->getWrapper()->hidden($val);
        return $this;
    }
}
