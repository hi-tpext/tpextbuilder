<?php

namespace tpext\builder\displayer;

use tpext\builder\traits\HasStorageDriver;
use tpext\builder\traits\HasImageDriver;
use tpext\builder\common\Module;

/**
 * MultipleFile class
 * @method $this  image()
 * @method $this  office()
 * @method $this  video()
 * @method $this  audio()
 * @method $this  pkg()
 */
class MultipleFile extends Field
{
    use HasStorageDriver;
    use HasImageDriver;

    protected $view = 'multiplefile';

    protected $js = [
        '/assets/tpextbuilder/js/webuploader/webuploader.min.js',
        '/assets/tpextbuilder/js/magnific-popup/jquery.magnific-popup.min.js',
    ];

    protected $css = [
        '/assets/tpextbuilder/js/webuploader/webuploader.css',
        '/assets/tpextbuilder/js/magnific-popup/magnific-popup.min.css',
        '/assets/tpextbuilder/css/uploadfiles.css',
    ];

    protected $canUpload = true;

    protected $showInput = true;

    protected $showChooseBtn = true;

    protected $showUploadBtn = true;

    protected $isInTable = false;

    protected $files = [];

    protected $cover = '/assets/tpextbuilder/images/cover/file.svg';

    protected $jsOptions = [
        'resize' => false,
        'duplicate' => true,
        'ext' => [
            //
            'jpg', 'jpeg', 'gif', 'wbmp', 'webp', 'png', 'bmp', 'ico', 'swf', 'psd', 'jpc', 'jp2', 'jpx', 'jb2', 'swc', 'iff', 'xbm', 'svg',
            //
            "flv", "mkv", "avi", "rm", "rmvb", "mpeg", "mpg", "ogv", "mov", "wmv", "mp4", "webm",
            //
            "ogg", "mp3", "wav", "mid",
            //
            "rar", "zip", "tar", "gz", "7z", "bz2", "cab", "iso",
            //
            "doc", "docx", "xls", "xlsx", "ppt", "pptx", "pdf", "txt", "md",
            //
            "xml", "json",
        ],
        'multiple' => true,
        'mimeTypes' => '*/*',
        'swf_url' => '/assets/tpextbuilder/js/webuploader/Uploader.swf',
        'fileSingleSizeLimit' => 5 * 1024 * 1024,
        'fileNumLimit' => 5,
        'fileSizeLimit' => 0,
        'thumbnailWidth' => 80,
        'thumbnailHeight' => 80,
        'isImage' => false,
        'istable' => false,
    ];

    protected $extTypes = [
        'image' => ['jpg', 'jpeg', 'gif', 'wbmp', 'webp', 'png', 'bmp', 'ico', 'swf', 'psd', 'jpc', 'jp2', 'jpx', 'jb2', 'swc', 'iff', 'xbm', 'svg'],
        'office' => ["doc", "docx", "xls", "xlsx", "ppt", "pptx", "pdf"],
        'video' => ["flv", "mkv", "avi", "rm", "rmvb", "mpeg", "mpg", "ogv", "mov", "wmv", "mp4", "webm"],
        'audio' => ["ogg", "mp3", "wav", "mid"],
        'pkg' => ["rar", "zip", "tar", "gz", "7z", "bz2", "cab", "iso"],
    ];

    protected $coverList = [
        'image' => '/assets/tpextbuilder/images/cover/image.svg',
        'office' => '/assets/tpextbuilder/images/cover/office.svg',
        'video' => '/assets/tpextbuilder/images/cover/video.svg',
        'audio' => '/assets/tpextbuilder/images/cover/audio.svg',
        'pkg' => '/assets/tpextbuilder/images/cover/pkg.svg',
    ];

    /**
     * Undocumented function
     *
     * @param array $val
     * @return $this
     */
    public function default($val = [])
    {
        $this->default = $val;
        return $this;
    }

    /**
     * 可以上传
     *
     * @param boolean $val
     * @return $this
     */
    public function canUpload($val)
    {
        $this->canUpload = $val;
        return $this;
    }

    /**
     * 是否显示文件输入框
     *
     * @param boolean $val
     * @return $this
     */
    public function showInput($val)
    {
        $this->showInput = $val;
        return $this;
    }

    /**
     * 是否显示[选择已上传文件]按钮
     *
     * @param boolean $val
     * @return $this
     */
    public function showChooseBtn($val)
    {
        $this->showChooseBtn = $val;
        return $this;
    }

    /**
     * 是否显示[上传新文件]按钮
     *
     * @param boolean $val
     * @return $this
     */
    public function showUploadBtn($val)
    {
        $this->showUploadBtn = $val;
        return $this;
    }

    /**
     * 同时禁用[上传新文件][选择已上传文件]
     * 可通过cover图片控制
     * 
     * @return $this
     */
    public function disableButtons()
    {
        $this->showUploadBtn = false;
        $this->showChooseBtn = false;

        return $this;
    }

    /**
     * 累计文件数量限制
     * 
     * @param int $val
     * @return $this
     */
    public function limit($val)
    {
        $this->jsOptions['fileNumLimit'] = $val;
        return $this;
    }

    /**
     * 占位图片，当为文件列表空时显示
     *
     * @param string $val
     * @return $this
     */
    public function cover($val)
    {
        $this->cover = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $is
     * @return $this
     */
    public function setIsInTable($val = true)
    {
        $this->isInTable = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function smallSize()
    {
        $this->jsOptions['thumbnailWidth'] = 50;
        $this->jsOptions['thumbnailHeight'] = 50;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function mediumSize()
    {
        $this->jsOptions['thumbnailWidth'] = 120;
        $this->jsOptions['thumbnailHeight'] = 120;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function bigSize()
    {
        $this->jsOptions['thumbnailWidth'] = 240;
        $this->jsOptions['thumbnailHeight'] = 240;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function largeSize()
    {
        $this->jsOptions['thumbnailWidth'] = 480;
        $this->jsOptions['thumbnailHeight'] = 480;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param integer $w
     * @param integer $h
     * @return $this
     */
    public function thumbSize($w, $h)
    {
        $this->jsOptions['thumbnailWidth'] = $w;
        $this->jsOptions['thumbnailHeight'] = $h;

        return $this;
    }

    public function render()
    {
        $this->canUpload = !$this->readonly && $this->canUpload;

        if (!$this->canUpload) {
            $this->cover = '';
            if (empty($this->default)) {
                $this->default = '/assets/tpextbuilder/images/ext/0.png';
            }
        }

        if ($this->canUpload) {

            if (!isset($this->jsOptions['upload_url']) || empty($this->jsOptions['upload_url'])) {
                $token = $this->getCsrfToken();
                $this->jsOptions['upload_url'] = url($this->getUploadUrl(), [
                    'utype' => 'webuploader',
                    'token' => $token,
                    'driver' => $this->getStorageDriver(),
                    'is_rand_name' => $this->isRandName(),
                    'image_driver' => $this->getImageDriver(),
                    'image_commonds' => $this->getImageCommands()
                ]);
            }

            if (!isset($this->jsOptions['chooseUrl']) || empty($this->jsOptions['chooseUrl'])) {
                $this->jsOptions['chooseUrl'] = url(Module::getInstance()->getChooseUrl()) . '?';
            }
        }

        if ($this->extKey) { //table 或 items 中
            $this->showInput = false; //隐藏输入框
            $this->getWrapper()->addClass('in-table-in-items');
        }

        $vars = $this->commonVars();

        $this->value = $vars['value'];

        if (!empty($this->value)) {
            $this->files = is_array($this->value) ? $this->value : explode(',', $this->value);
        } else if (!empty($this->default)) {
            $this->files = is_array($this->default) ? $this->default : explode(',', $this->default);
        }

        $this->files = array_filter($this->files, 'strlen');

        $this->jsOptions['canUpload'] = $this->canUpload;
        $this->jsOptions['showInput'] = $this->showInput;
        $this->jsOptions['showChooseBtn'] = $this->showChooseBtn;
        $this->jsOptions['showUploadBtn'] = $this->showUploadBtn;
        $this->jsOptions['isInTable'] = $this->isInTable;
        $this->jsOptions['cover'] = $this->cover;

        $vars = array_merge($vars, [
            'jsOptions' => $this->jsOptions,
            'canUpload' => $this->canUpload,
            'showInput' => $this->showInput,
            'showChooseBtn' => $this->showChooseBtn,
            'showUploadBtn' => $this->showUploadBtn,
            'isInTable' => $this->isInTable,
            'files' => $this->files,
            'cover' => $this->cover,
            'inputType' => $this->showInput ? 'text' : 'hidden',
        ]);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }

    /**
     * Undocumented function
     *
     * @param string|array $types ['jpg', 'jpeg', 'gif'] or 'jpg,jpeg,gif'
     * @return $this
     */
    public function extTypes($types)
    {
        $this->jsOptions['ext'] = is_string($types) ? explode(',', $types) : $types;
        return $this;
    }

    public function __call($name, $arguments)
    {
        if (isset($this->extTypes[$name])) {
            $this->jsOptions['ext'] = $this->extTypes[$name];
            if ($this->cover) {
                $this->cover = $this->coverList[$name];
            }
            return $this;
        }

        throw new \UnexpectedValueException('未知调用:' . $name);
    }
}
