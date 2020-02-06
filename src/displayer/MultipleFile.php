<?php

namespace tpext\builder\displayer;

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

    protected $jsOptions = [
        'resize' => false,
        'duplicate' => false,
        'ext' => ['jpg', 'jpeg', 'gif', 'wbmp', 'webpg', 'png',
            'zip', "flv", "swf", "mkv", "avi", "rm", "rmvb", "mpeg", "mpg",
            "ogg", "ogv", "mov", "wmv", "mp4", "webm", "mp3", "wav", "mid",
            "rar", "zip", "tar", "gz", "7z", "bz2", "cab", "iso",
            "doc", "docx", "xls", "xlsx", "ppt", "pptx", "pdf", "txt", "md", "xml", "json"],
        'size' => 20 * 1024 * 1024,
        'multiple' => true,
        'mimeTypes' => '*/*',
        'swf_url' => '/assets/tpextbuilder/js/webuploader/Uploader.swf',
        'limit' => 10,
    ];

    protected $files = [];

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
            $token = md5('uploadtoken' . time() . uniqid());

            session('uploadtoken', $token);

            $this->jsOptions['upload_url'] = url('/tpextbuilder/admin/upload/upfiles', ['type' => 'webuploader', 'token' => $token]);
        }

        $vars = $this->commonVars();

        if (!empty($this->value)) {
            $this->files = is_array($this->value) ? $this->value : explode(',', $this->value);
        } else if (!empty($this->default)) {
            $this->files = is_array($this->default) ? $this->default : explode(',', $this->default);
        }

        $vars = array_merge($vars, [
            'jsOptions' => $this->jsOptions,
            'files' => $this->files,
        ]);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }
}
