<?php

namespace tpext\builder\displayer;

use tpext\think\View;

class Html extends Field
{
    protected $view = 'html';

    protected $isInput = false;

    protected $content = null;

    public function __construct($html, $label = '')
    {
        $this->label = $label;
        $this->default = $html;
        $this->name = 'html' . mt_rand(100, 999);
    }

    public function created($fieldType = '')
    {
        $this->getWrapper()->setName($this->name);
        return parent::created($fieldType);
    }

    /**
     * Undocumented function
     *
     * @param string $template
     * @param array $vars
     * @return $this
     */
    public function fetch($template = '', $vars = [])
    {
        $this->content = new View($template);
        $this->content->assign($vars);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $content
     * @param array $vars
     * @return $this
     */
    public function display($content = '', $vars = [])
    {
        $this->content = new View($content);
        $this->content->assign($vars)->isContent(true);
        return $this;
    }

    public function renderValue()
    {
        $value = parent::renderValue();

        if ($this->content) {
            return $this->content->assign(['__val__' => $value])->getContent();
        }

        return $value;
    }
}
