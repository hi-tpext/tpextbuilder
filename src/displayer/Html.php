<?php

namespace tpext\builder\displayer;

use think\response\View as ViewShow;

class Html extends Field
{
    protected $view = 'html';

    /**
     * Undocumented variable
     *
     * @var ViewShow
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
     * @param array $config
     * @return $this
     */
    public function fetch($template = '', $vars = [], $config = [])
    {
        $this->content = view($template);

        $this->content->assign($vars)->config($config);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $content
     * @param array $vars
     * @param array $config
     * @return $this
     */
    public function display($content = '', $vars = [], $config = [])
    {
        $this->content = view($content);

        $this->content->assign($vars)->config($config)->isContent(true);
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
