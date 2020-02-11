<?php

namespace tpext\builder\displayer;

/**
 * MultipleFile class
 * @method MultipleFile   image()
 * @method MultipleFile   office()
 * @method MultipleFile   video()
 * @method MultipleFile   audio()
 * @method MultipleFile   pkg()
 */
class MultipleFile extends Field
{
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

    protected $files = [];

    protected $jsOptions = [
        'resize' => false,
        'duplicate' => true,
        'ext' => [
            //
            'jpg', 'jpeg', 'gif', 'wbmp', 'webpg', 'png', 'bmp',
            //
            "flv", "swf", "mkv", "avi", "rm", "rmvb", "mpeg", "mpg", "ogv", "mov", "wmv", "mp4", "webm",
            //
            "ogg", "mp3", "wav", "mid",
            //
            "rar", "zip", "tar", "gz", "7z", "bz2", "cab", "iso",
            //
            "doc", "docx", "xls", "xlsx", "ppt", "pptx", "pdf", "txt", "md",
            //
            "xml", "json"],
        'multiple' => true,
        'mimeTypes' => '*/*',
        'swf_url' => '/assets/tpextbuilder/js/webuploader/Uploader.swf',
        'fileSingleSizeLimit' => 5 * 1024 * 1024,
        'fileNumLimit' => 5,
        'fileSizeLimit' => 0,
        'thumbnailWidth' => 165,
        'thumbnailHeight' => 110,
    ];

    protected $extTypes = [
        'image' => ['jpg', 'jpeg', 'gif', 'wbmp', 'webpg', 'png', 'bmp'],
        'office' => ["doc", "docx", "xls", "xlsx", "ppt", "pptx", "pdf"],
        'video' => ["flv", "swf", "mkv", "avi", "rm", "rmvb", "mpeg", "mpg", "ogv", "mov", "wmv", "mp4", "webm"],
        'audio' => ["ogg", "mp3", "wav", "mid"],
        'pkg' => ["rar", "zip", "tar", "gz", "7z", "bz2", "cab", "iso"],
    ];

    /**
     * Undocumented function
     *
     * @param array $val
     * @return $this
     */
    function default($val = []) {
        $this->default = $val;
        return $this;
    }

    /**
     * Undocumented function
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
     * Undocumented function
     * fileNumLimit
     * @param int $val
     * @return $this
     */
    public function limit($val)
    {
        $this->jsOptions['fileNumLimit'] = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $options
     * @return $this
     */
    public function jsOptions($options)
    {
        $this->jsOptions = array_merge($this->jsOptions, $options);
        return $this;
    }

    public function render()
    {
        if (!isset($this->jsOptions['upload_url']) || empty($this->jsOptions['upload_url'])) {
            $token = session('uploadtoken') ? session('uploadtoken') : md5('uploadtoken' . time() . uniqid());

            session('uploadtoken', $token);

            $this->jsOptions['upload_url'] = url('/tpextbuilder/admin/upload/upfiles', ['type' => 'webuploader', 'token' => $token]);
        }

        $vars = $this->commonVars();

        if (!empty($this->value)) {
            $this->files = is_array($this->value) ? $this->value : explode(',', $this->value);
        } else if (!empty($this->default)) {
            $this->files = is_array($this->default) ? $this->default : explode(',', $this->default);
        }

        $this->jsOptions['canUpload'] = $this->canUpload && empty($this->tableRowKey);

        $vars = array_merge($vars, [
            'jsOptions' => $this->jsOptions,
            'canUpload' => $this->canUpload && empty($this->tableRowKey),
            'files' => $this->files,
        ]);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }

    public function __call($name, $arguments)
    {
        if (isset($this->extTypes[$name])) {
            $this->jsOptions['ext'] = $this->extTypes[$name];
            return $this;
        }

        throw new \UnexpectedValueException('未知调用');
    }
}
