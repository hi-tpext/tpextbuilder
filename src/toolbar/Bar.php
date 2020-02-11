<?php

namespace tpext\builder\toolbar;

use think\response\View as ViewShow;
use tpext\builder\common\Builder;
use tpext\builder\common\Plugin;
use tpext\builder\common\Renderable;

class Bar implements Renderable
{
    protected $view = '';

    protected $class = 'btn-default';

    protected $name = '';

    protected $label = '';

    protected $attr = '';

    protected $style = '';

    protected $js = [];

    protected $css = [];

    protected $script = [];

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
        return 'tool-' . $this->name;
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
        $vars = $this->commonVars();

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }

    protected function getViewInstance()
    {
        $template = Plugin::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'toolbar', $this->view . '.html']);

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
        $vars = [
            'id' => $this->getId(),
            'label' => $this->label,
            'name' => $this->getName(),
            'class' => ' ' . $this->class,
            'attr' => $this->attr . (empty($this->style) ? '' : ' style="' . $this->style . '"'),
        ];

        return $vars;
    }
}
