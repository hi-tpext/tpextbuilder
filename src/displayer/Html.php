<?php

namespace tpext\builder\displayer;

class Html extends Field
{
    protected $view = 'html';

    /**
     * Undocumented variable
     *
     * @var \think\response\View
     */
    protected $content;

    public function created($fieldType = '')
    {
        parent::created($fieldType);

        $this->value = $this->label ? $this->label : $this->name;

        $this->name = 'html' . mt_rand(100, 999);

        return $this;
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
        $this->content = view($template);

        $value = $this->renderValue();

        $vars = array_merge($vars, [
            $this->name => $value,
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
        $this->content = view($content);

        $value = $this->renderValue();

        $vars = array_merge($vars, [
            $this->name => $value,
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
