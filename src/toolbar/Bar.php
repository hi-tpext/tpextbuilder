<?php

namespace tpext\builder\toolbar;

use tpext\builder\common\Builder;
use tpext\builder\common\Module;
use tpext\builder\inface\Renderable;
use tpext\builder\traits\HasDom;
use tpext\think\View;
use tpext\common\ExtLoader;

class Bar implements Renderable
{
    use HasDom;

    protected $name = '';

    protected $view = '';

    protected $extKey = '';

    protected $icon = '';

    protected $href = 'javascript:;';

    protected $__href__ = '';

    protected $label = '';

    protected $script = [];

    protected $useLayer = true;

    protected $layerSize;

    protected $pullRight = false;

    /**
     * Undocumented variable
     *
     * @var \Closure
     */
    protected $rendering = null;

    public function __construct($name, $label = '')
    {
        $this->name = $name;
        $this->label = $label;
        $this->class = 'btn-default';
    }

    /**
     * Undocumented function
     *
     * @param string $barType
     * @return $this
     */
    public function created($barType = '')
    {
        $barType = $barType ? $barType : get_called_class();

        $barType = lcfirst($barType);

        $defaultClass = BWrapper::hasDefaultBarClass($barType);

        if (!empty($defaultClass)) {
            $this->class = $defaultClass;
        }

        return $this;
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
        $this->href = (string)$val;

        if (!Builder::checkUrl($this->href)) {
            $this->addClass('hidden disabled');
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @param array|string $size
     * @return $this
     */
    public function useLayer($val, $size = [])
    {
        $this->useLayer = $val;

        if (!empty($size)) {
            $this->layerSize = is_array($size) ? implode(',', $size) : $size;
        }

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
     * @param boolean $val
     * @return $this
     */
    public function pullRight($val = true)
    {
        $this->pullRight = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isPullRight()
    {
        return $this->pullRight;
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
    public function beforRender()
    {
        if (!empty($this->script)) {
            Builder::getInstance()->addScript($this->script);
        }

        ExtLoader::trigger('tpext_bar_befor_render', $this);

        if ($this->rendering instanceof \Closure) {
            $this->rendering->call($this, $this);
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
        $template = Module::getInstance()->getViewsPath() . 'toolbar' . DIRECTORY_SEPARATOR . $this->view . '.html';

        $viewshow = new View($template);

        return $viewshow;
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
     * Undocumented function
     *
     * @return array
     */
    public function commonVars()
    {
        $this->useLayer = $this->useLayer && !empty($this->href) && !preg_match('/javascript:.*/i', $this->href) && !preg_match('/^#.*/i', $this->href);

        if (empty($this->layerSize)) {
            $config = Module::getInstance()->getConfig();
            $this->layerSize = $config['layer_size'];
        }

        if (($this->__href__ || $this->href) && strpos($this->attr, 'data-layer-size=') === false) {
            $this->addAttr('data-layer-size="' . $this->layerSize . '"');
        }

        if (strpos($this->attr, 'target=') !== false) {
            $this->useLayer = false;
        }

        $vars = [
            'id' => $this->getId(),
            'label' => $this->label,
            'name' => $this->getName(),
            'class' => $this->getClass(),
            'href' => empty($this->__href__) ? $this->href : $this->__href__,
            'icon' => $this->icon,
            'attr' => $this->getAttrWithStyle(),
            'useLayer' => $this->useLayer,
            'pullRight' => $this->pullRight
        ];

        $customVars = $this->customVars();

        if (!empty($customVars)) {
            $vars = array_merge($vars, $customVars);
        }

        return $vars;
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
}
