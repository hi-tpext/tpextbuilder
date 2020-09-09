<?php

namespace tpext\builder\common;

use tpext\builder\inface\Renderable;

class Content implements Renderable
{
    /**
     * Undocumented variable
     *
     * @var \think\response\View
     */
    protected $content;

    protected $partial = false;

    /**
     * Undocumented function
     *
     * @return string|\think\response\View
     */
    public function render()
    {
        if ($this->partial) {
            return $this->content;
        }

        return $this->content->getContent();
    }

    public function __toString()
    {
        $this->partial = false;
        return $this->render();
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function partial($val = true)
    {
        $this->partial = $val;
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
     * @param string $template
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

    public function beforRender()
    {
        return $this;
    }
}
