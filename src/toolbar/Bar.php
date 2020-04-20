<?php

namespace tpext\builder\toolbar;

use think\response\View as ViewShow;
use tpext\builder\common\Builder;
use tpext\builder\common\Module;
use tpext\builder\common\Renderable;

class Bar implements Renderable
{
    protected $view = '';

    protected $extKey = '';

    protected $class = 'btn-default';

    protected $name = '';

    protected $icon = '';

    protected $href = 'javascript:;';

    protected $__href__ = '';

    protected $label = '';

    protected $attr = '';

    protected $style = '';

    protected $script = [];

    protected $useLayer = true;

    protected $layerSize = ['90%', '90%'];

    public function __construct($name, $label = '')
    {
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
        return 'bar-' . $this->name . preg_replace('/[^\w\-]/', '', $this->extKey);
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
    public function icon($val)
    {
        $this->icon = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function href($val)
    {
        $this->href = $val;
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
     * @param boolean $val
     * @param array $size
     * @return $this
     */
    public function useLayer($val, $size = ['90%', '90%'])
    {
        $this->useLayer = $val;
        $this->layerSize = $size;

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
    public function extKey($val)
    {
        $this->extKey = $val;
        return $this;
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

    public function beforRender()
    {
        if (!empty($this->js)) {
            Builder::getInstance()->addJs($this->js);
        }

        if (!empty($this->css)) {
            Builder::getInstance()->addCss($this->css);
        }

        if (!empty($this->script)) {
            Builder::getInstance()->addScript($this->script);
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return mixed
     */
    public function render()
    {
        return '<!--empty bar-->';
    }

    protected function getViewInstance()
    {
        $template = Module::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'toolbar', $this->view . '.html']);

        $viewshow = new ViewShow($template);

        return $viewshow;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function commonVars()
    {
        $this->useLayer = $this->useLayer && !empty($this->href) && !preg_match('/javascript:.*/i', $this->href) && !preg_match('/^#.*/i', $this->href);
        
        $vars = [
            'id' => $this->getId(),
            'label' => $this->label,
            'name' => $this->getName(),
            'class' => ' ' . $this->class,
            'href' => empty($this->__href__) ? $this->href : $this->__href__,
            'icon' => $this->icon,
            'attr' => $this->attr . (empty($this->style) ? '' : ' style="' . $this->style . '"'),
            'useLayer' => $this->useLayer,
            'layerSize' => implode(',', $this->layerSize),
        ];

        return $vars;
    }
}
