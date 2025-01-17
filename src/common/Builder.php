<?php

namespace tpext\builder\common;

use tpext\think\App;
use tpext\think\View;
use think\facade\Session;
use tpext\common\ExtLoader;
use tpext\builder\tree\ZTree;
use tpext\builder\inface\Auth;
use tpext\builder\tree\JSTree;
use tpext\builder\inface\Renderable;

class Builder implements Renderable
{
    protected $view = '';

    protected $layout = '';

    protected $title = '';

    protected $desc = '';

    protected $csrf_token = '';

    /**
     * Undocumented variable
     *
     * @var Row[]
     */
    protected $rows = [];

    /**
     * Undocumented variable
     *
     * @var Row
     */
    protected $__row__ = null;

    protected $js = [];

    protected $customJs = [];

    protected $css = [];

    protected $customCss = [];

    protected $styleSheet = [];

    protected $script = [];

    protected $notify = [];

    protected $layer;

    protected $commonJs = [
        '/assets/tpextbuilder/js/jquery-validate/jquery.validate.min.js',
        '/assets/tpextbuilder/js/jquery-validate/messages.min.js',
        '/assets/tpextbuilder/js/layer/layer.js',
        '/assets/tpextbuilder/js/tpextbuilder.js',
    ];

    protected $commonCss = [
        '/assets/tpextbuilder/css/tpextbuilder.css'
    ];

    /**
     * Undocumented variable
     *
     * @var Auth
     */
    protected static $auth;

    protected static $minify = false;

    protected static $aver = '1.0';

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
     * @return static
     */
    public static function getInstance($title = '', $desc = '')
    {
        if (self::$instance == null) {
            self::$instance = new static($title, $desc);
            self::$instance->created();

            ExtLoader::trigger('tpext_create_builder', self::$instance);
        } else {
            if ($title) {
                self::$instance->title($title);
            }
            if ($desc) {
                self::$instance->desc($desc);
            }
        }

        return self::$instance;
    }

    /**
     * 销毁实列
     *
     * @return void
     */
    public static function destroyInstance()
    {
        if (self::$instance) {
            self::$instance->destroy();
            self::$instance = null;
        }
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    protected function created()
    {
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function title($val)
    {
        $this->title = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function desc($val)
    {
        $this->desc = $val;
        return $this;
    }

    /**
     * Undocumented function
     * 
     * @param mixed $val
     * @return $this
     */
    public function layout($val)
    {
        $this->layout = $val;
        return $this;
    }

    /**
     * 设置视图模板路径，避免不同的应用中使用Builder时模板缓存冲突
     *
     * @param string $template
     * @return $this
     */
    public function setView($template)
    {
        $this->view = $template;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getCsrfToken()
    {
        if (!$this->csrf_token) {

            $token = Session::get('_csrf_token_');

            if (empty($token)) {
                $token = md5('_csrf_token_' . time() . uniqid());
                Session::set('_csrf_token_', $token);
            }

            $this->csrf_token = $token;
        }

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
     * 添加自定义js，不会被minify
     *
     * @param array|string $val
     * @return $this
     */
    public function customJs($val)
    {
        if (!is_array($val)) {
            $val = [$val];
        }
        $this->customJs = array_merge($this->customJs, $val);
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
     * 添加自定义css，不会被minify
     *
     * @param array|string $val
     * @return $this
     */
    public function customCss($val)
    {
        if (!is_array($val)) {
            $val = [$val];
        }
        $this->customCss = array_merge($this->customCss, $val);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|string $val
     * @return $this
     */
    public function removeJs($val)
    {
        if (!is_array($val)) {
            $val = [$val];
        }

        foreach ($this->js as $k => $j) {
            if (in_array($j, $val)) {
                unset($this->js[$k]);
            }
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|string $val
     * @return $this
     */
    public function removeCss($val)
    {
        if (!is_array($val)) {
            $val = [$val];
        }

        foreach ($this->css as $k => $c) {
            if (in_array($c, $val)) {
                unset($this->css[$k]);
            }
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @param string $newVal
     * @return $this
     */
    public function replaceJs($val, $newVal)
    {
        foreach ($this->js as $k => $j) {
            if ($val == $j) {
                $this->js[$k] = $newVal;
                break;
            }
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @param string $newVal
     * @return $this
     */
    public function replaceCss($val, $newVal)
    {
        foreach ($this->css as $k => $c) {
            if ($val == $c) {
                $this->css[$k] = $newVal;
                break;
            }
        }

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
     * @param array|string $val
     * @return $this
     */
    public function addStyleSheet($val)
    {
        if (!is_array($val)) {
            $val = [$val];
        }
        $this->styleSheet = array_merge($this->styleSheet, $val);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getJs()
    {
        return $this->js;
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
     * @return array
     */
    public function getStyleSheet()
    {
        return $this->styleSheet;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getCss()
    {
        return $this->css;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function clearRows()
    {
        $this->rows = [];
        $this->__row__ = null;

        return $this;
    }

    /**
     * Undocumented function
     * lightyear.notify('修改成功，页面即将自动跳转~', 'success', 5000, 'mdi mdi-emoticon-happy', 'top', 'center');
     * @param string $msg
     * @param string $type
     * @param integer $delay
     * @param string $icon
     * @param string $from
     * @param string $align
     * @return $this
     */
    public function notify($msg, $type = 'info', $delay = 2000, $icon = '', $from = 'top', $align = 'center')
    {
        $this->notify = [$msg, $type, $delay, $icon, $from, $align];
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getNotify()
    {
        return $this->notify;
    }

    /**
     * Undocumented function
     *
     * @return Row
     */
    public function row()
    {
        $row = Row::make();
        $this->rows[] = $row;
        $this->__row__ = $row;
        return $row;
    }

    /**
     * Undocumented function
     *
     * @param integer|string $size
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
     * @param integer|string $size col大小
     * @return Form
     */
    public function form($size = 12)
    {
        return $this->column($size)->form();
    }

    /**
     * 获取一个表格
     *
     * @param integer|string $size col大小
     * @return Table
     */
    public function table($size = 12)
    {
        return $this->column($size)->table();
    }

    /**
     * 获取一个工具栏
     *
     * @param integer|string $size col大小
     * @return Toolbar
     */
    public function toolbar($size = 12)
    {
        return $this->column($size)->toolbar();
    }

    /**
     * 默认获取一个ZTree树
     * 
     * @param integer|string $size col大小
     * @return ZTree
     */
    public function tree($size = 12)
    {
        return $this->column($size)->tree();
    }

    /**
     * 获取一个ZTree树
     *
     * @param integer|string $size col大小
     * @return ZTree
     */
    public function zTree($size = 12)
    {
        return $this->column($size)->zTree();
    }

    /**
     * 获取一个JSTree树
     *
     * @param integer|string $size col大小
     * @return JSTree
     */
    public function jsTree($size = 12)
    {
        return $this->column($size)->jsTree();
    }

    /**
     * 获取一自定义内容
     *
     * @param integer|string $size col大小
     * @return Content
     */
    public function content($size = 12)
    {
        return $this->column($size)->content();
    }

    /**
     * 获取一tab内容
     *
     * @param integer|string $size col大小
     * @return Tab
     */
    public function tab($size = 12)
    {
        return $this->column($size)->tab();
    }

    /**
     * 获取一Swiper
     *
     * @param integer|string $size col大小
     * @return Swiper
     */
    public function swiper($size = 12)
    {
        return $this->column($size)->swiper();
    }

    /**
     * 获取layer
     *
     * @return Layer
     */
    public function layer(...$arguments)
    {
        if (!$this->layer) {
            $this->layer = Column::makeWidget('Layer', $arguments);
        }

        return $this->layer;
    }

    /**
     * Undocumented function
     *
     * @param string $template
     * @param array $vars
     * @param integer|string $size col大小
     * @return $this
     */
    public function fetch($template = '', $vars = [], $size = 12)
    {
        $this->content($size)->fetch($template, $vars);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $content
     * @param array $vars
     * @param integer|string $size col大小
     * @return $this
     */
    public function display($content = '', $vars = [], $size = 12)
    {
        $this->content($size)->display($content, $vars);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array|string
     */
    public function commonJs()
    {
        return $this->commonJs;
    }

    /**
     * Undocumented function
     *
     * @return array|string
     */
    public function commonCss()
    {
        return $this->commonCss;
    }

    public function beforRender()
    {
        foreach ($this->rows as $row) {
            $row->beforRender();
        }

        $this->addJs($this->commonJs());
        $this->addCss($this->commonCss());
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return void
     */
    public static function minify($val)
    {
        static::$minify = $val;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public static function isMinify()
    {
        return static::$minify;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return void
     */
    public static function aver($val)
    {
        static::$aver = $val;
    }

    /**
     * Undocumented function
     *
     * @param string|Auth $class
     * @return void
     */
    public static function auth($class)
    {
        static::$auth = $class;
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @return boolean
     */
    public static function checkUrl($url)
    {
        //如果不是完整的[moudle/controller/action]格式
        if (preg_match('/^\w+$/', $url) || preg_match('/^\w+(\.\w+)?\/\w+$/', $url)) {
            $url = url($url);
        }

        if (!empty(static::$auth)) {

            return static::$auth::checkUrl($url);
        }

        return true;
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
     * @return View
     */
    public function render()
    {
        if ($this->layer) {
            return $this->layer->getViewShow();
        }

        $this->beforRender();

        if (empty($this->view)) {
            $this->view = Module::getInstance()->getViewsPath() . 'content.html';
        }

        if (!empty($this->notify)) {

            $this->script[] = "lightyear.notify('{$this->notify[0]}', '{$this->notify[1]}', {$this->notify[2]}, '{$this->notify[3]}', '{$this->notify[4]}', '{$this->notify[5]}');";
        }

        if (static::$minify) {
            $this->js = $this->customJs;
            $this->css = $this->customCss;
        } else {
            $this->js = array_merge($this->js, $this->customJs);
            $this->css = array_merge($this->css, $this->customCss);
        }

        foreach ($this->css as &$c) {
            if (strpos($c, '?') == false && strpos($c, 'http') == false) {
                $c .= '?aver=' . static::$aver;
            }
        }

        unset($c);

        foreach ($this->js as &$j) {
            if (strpos($j, '?') == false && strpos($j, 'http') == false) {
                $j .= '?aver=' . static::$aver;
            }
        }

        unset($j);

        $__blang = include Module::getInstance()->getRoot() . 'src' . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . App::getDefaultLang() . '.php';

        $vars = [
            'title' => $this->title ? $this->title : '',
            'desc' => $this->desc,
            'rows' => $this->rows,
            'js' => array_unique($this->js),
            'css' => array_unique($this->css),
            'stylesheet' => implode('', array_unique($this->styleSheet)),
            'script' => implode('', array_unique($this->script)),
            '__blang' => json_encode($__blang, JSON_UNESCAPED_UNICODE),
        ];

        $share = View::getShare();

        if (empty($share['admin_layout'])) {
            if (empty($this->layout)) {
                $this->layout = Module::getInstance()->getViewsPath() . 'layout.html';
            }

            $vars = array_merge($vars, [
                'admin_layout' => $this->layout,
                'admin_js' => [
                    '/assets/lightyearadmin/js/jquery.min.js',
                    '/assets/lightyearadmin/js/bootstrap.min.js',
                    '/assets/lightyearadmin/js/jquery.lyear.loading.js',
                    '/assets/lightyearadmin/js/bootstrap-notify.min.js',
                    '/assets/lightyearadmin/js/jconfirm/jquery-confirm.min.js',
                    '/assets/lightyearadmin/js/lightyear.js',
                    '/assets/lightyearadmin/js/main.min.js',
                ],
                'admin_css' => [
                    '/assets/lightyearadmin/css/bootstrap.min.css',
                    '/assets/lightyearadmin/css/materialdesignicons.min.css',
                    '/assets/lightyearadmin/css/animate.css',
                    '/assets/lightyearadmin/css/style.min.css',
                    '/assets/lightyearadmin/js/jconfirm/jquery-confirm.min.css',
                ]
            ]);
        }

        View::share([
            '__token__' => $this->getCsrfToken(),
            'admin_page_title' => $this->desc,
            'admin_page_position' => $this->title
        ]);

        $customVars = $this->customVars();

        if (!empty($customVars)) {
            $vars = array_merge($vars, $customVars);
        }

        $viewshow = new View($this->view);

        return $viewshow->assign($vars);
    }

    public function __toString()
    {
        return $this->render()->getContent();
    }

    public function destroy()
    {
        foreach ($this->rows as $row) {
            $row->destroy();
        }

        $this->rows = null;
    }
}
