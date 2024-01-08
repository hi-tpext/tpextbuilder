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

        $value = $this->renderValue();

        $vars = array_merge($vars, [
            '__val__' => $value
        ]);

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

        $value = $this->renderValue();

        $vars = array_merge($vars, [
            '__val__' => $value
        ]);

        $this->content->assign($vars)->isContent(true);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return mixed
     */
    public function render()
    {
        if ($this->content) {
            $this->value = $this->content->getContent();
        }

        return parent::render();
    }
}
