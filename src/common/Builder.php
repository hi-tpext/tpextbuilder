<?php

namespace tpext\builder\common;

use think\facade\View;
use think\response\View as ViewShow;
use tpext\builder\common\Plugin;
use tpext\myadmin\common\Module;

class Builder implements Renderable
{
    private $view = '';

    protected $title = '';

    protected $desc = null;

    protected $rows = [];

    protected $__row__ = null;

    protected $js = [];

    protected $css = [];

    protected $style = [];

    protected $script = [];

    protected static $instance = null;

    protected function __construct($title, $desc)
    {
        $this->title = $title;
        $this->desc = $desc;
    }

    /**
     * Undocumented function
     *
     * @param string $title
     * @param string $desc
     * @return $this
     */
    public static function getInstance($title = 'tpext-builder', $desc = '')
    {
        if (static::$instance == null) {
            static::$instance = new static($title, $desc);
        }

        return static::$instance;
    }

    /**
     * Undocumented function
     *
     * @param array $val
     * @return $this
     */
    public function addJs($val)
    {
        $this->js = array_merge($this->js, $val);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $val
     * @return $this
     */
    public function addCss($val)
    {
        $this->css = array_merge($this->css, $val);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return Row
     */
    public function row()
    {
        $row = new Row();
        $this->rows[] = $row;
        $this->__row__ = $row;
        return $row;
    }

    /**
     * Undocumented function
     *
     * @param integer $size
     * @return Column
     */
    public function column($size = 12)
    {
        if (!$this->__row__) {
            $this->row();
        }

        return $this->__row__->column($size);
    }

    /**
     * 获取一个form
     *
     * @param integer col大小
     * @return Form
     */
    public function form($size = 12)
    {
        return $this->column($size)->form();
    }

    /**
     * 获取一个表格
     *
     * @param integer col大小
     * @return Table
     */
    public function table($size = 12)
    {
        return $this->column($size)->table();
    }

    /**
     * Undocumented function
     *
     * @return mixed
     */
    public function render()
    {
        $this->view = Plugin::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'content.html']);

        $vars = [
            'title' => $this->title,
            'desc' => $this->desc,
            'rows' => $this->rows,
            'js' => array_unique($this->js),
            'css' => array_unique($this->css),
            'style' => $this->style,
            'script' => $this->script,
        ];

        $config = [];

        $view = new ViewShow($this->view);

        $instance = Module::getInstance();

        $config = $instance->setConfig(['page_title' => $this->title, 'position' => '编辑']);

        View::share(['admin' => $config]);

        return $view->assign($vars)->config($config);
    }
}
