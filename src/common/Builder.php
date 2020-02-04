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

    protected $csrf_token = '';

    /**
     * Undocumented variable
     *
     * @var array
     */
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
            static::$instance->csrf_token = csrf_token();
            View::share(['__token__' => static::$instance->csrf_token]);
        }

        return static::$instance;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getCsrfToken()
    {
        return $this->csrf_token;
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
     * @param array $val
     * @return $this
     */
    public function addScript($val)
    {
        $this->script = array_merge($this->script, $val);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $val
     * @return $this
     */
    public function addStyle($val)
    {
        $this->style = array_merge($this->style, $val);
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

    public function beforRender()
    {
        foreach ($this->rows as $row) {
            $row->beforRender();
        }
    }

    /**
     * Undocumented function
     *
     * @return mixed
     */
    public function render()
    {
        $this->beforRender();

        $this->view = Plugin::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'content.html']);

        $this->js[] = '/assets/tpextbuilder/js/tpextbuilder.js';

        $vars = [
            'title' => $this->title,
            'desc' => $this->desc,
            'rows' => $this->rows,
            'js' => array_unique($this->js),
            'css' => array_unique($this->css),
            'style' => implode('', array_unique($this->style)),
            'script' => implode('', array_unique($this->script)),
        ];

        $config = [];

        $view = new ViewShow($this->view);

        $instance = Module::getInstance();

        $config = $instance->setConfig(['page_title' => $this->title, 'position' => '编辑']);

        View::share(['admin' => $config]);

        return $view->assign($vars)->config($config);
    }
}
