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

    protected $minify = false;

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
    public static function getInstance($title = 'Page', $desc = '')
    {
        if (static::$instance == null) {
            static::$instance = new static($title, $desc);
            $token = csrf_token();
            static::$instance->csrf_token = $token;
            View::share(['__token__' => $token]);
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
     * @param array|string $val
     * @return $this
     */
    public function addJs($val)
    {
        if (!is_array($val)) {
            $val = [$val];
        }
        $this->js = array_merge($this->js, $val);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|string $val
     * @return $this
     */
    public function addCss($val)
    {
        if (!is_array($val)) {
            $val = [$val];
        }
        $this->css = array_merge($this->css, $val);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|string $val
     * @return $this
     */
    public function addScript($val)
    {
        if (!is_array($val)) {
            $val = [$val];
        }
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
        if (!is_array($val)) {
            $val = [$val];
        }
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

    /**
     * 获取一个工具栏
     *
     * @param integer col大小
     * @return Toolbar
     */
    public function toolbar($size = 12)
    {
        return $this->column($size)->toolbar();
    }

    /**
     * 获取一自定义内容
     *
     * @param integer col大小
     * @return Content
     */
    public function content($size = 12)
    {
        return $this->column($size)->content();
    }

    /**
     * 获取一自定义内容
     *
     * @param integer col大小
     * @return Tab
     */
    public function tab($size = 12)
    {
        return $this->column($size)->tab();
    }

    public function beforRender()
    {
        foreach ($this->rows as $row) {
            $row->beforRender();
        }

        $this->js[] = '/assets/tpextbuilder/js/tpextbuilder.js';
        $this->css[] = '/assets/tpextbuilder/css/tpextbuilder.css';
    }

    /**
     * Undocumented function
     *
     * @return mixed
     */
    public function render($partial = false)
    {
        $this->beforRender();

        $this->view = Plugin::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'content.html']);

        $vars = [
            'title' => $this->title,
            'desc' => $this->desc,
            'rows' => $this->rows,
            'js' => $this->minify ? [] : array_unique($this->js),
            'css' => $this->minify ? [] : array_unique($this->css),
            'style' => implode('', array_unique($this->style)),
            'script' => implode('', array_unique($this->script)),
        ];

        $view = new ViewShow($this->view);

        $instance = Module::getInstance();

        $config = $instance->setConfig(['page_title' => $this->desc, 'position' => $this->title]);

        View::share(['admin' => $config]);

        return $view->assign($vars);
    }
}
