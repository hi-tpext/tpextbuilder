<?php

namespace tpext\builder\displayer;

use tpext\builder\traits\HasStorageDriver;

class UEditor extends Field
{
    use HasStorageDriver;

    protected $view = 'ueditor';

    protected $minify = false;

    protected $js = [
        '/assets/builderueditor/ueditor.config.js',
    ];

    protected $configJsPath = '/assets/builderueditor/ueditor.all.min.js';

    protected $uploadUrl = '';

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function configJsPath($val)
    {
        $this->configJsPath = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function uploadUrl($val)
    {
        $this->uploadUrl = $val;
        return $this;
    }

    protected function editorScript()
    {
        if (!class_exists('\\tpext\\builder\\ueditor\\common\\Resource')) {
            $this->js = [];
            $this->script[] = 'layer.alert("未安装ueditor资源包！<pre>composer require ichynul/builder-ueditor</pre>");';
            return;
        }

        $inputId = $this->getId();

        $script = <<<EOT

        var ue = UE.getEditor('{$inputId}');

EOT;
        $this->script[] = $script;

        return $script;
    }

    public function beforRender()
    {
        $this->js[] = $this->configJsPath;

        if (!$this->readonly) {
            $this->editorScript();
        }

        return parent::beforRender();
    }

    public function render()
    {
        if (empty($this->uploadUrl)) {

            $token = $this->getCsrfToken();

            $this->uploadUrl = url('/admin/upload/ueditor', ['token' => $token, 'driver' => $this->getStorageDriver(), 'is_rand_name' => $this->isRandName()])->__toString();
        }

        $vars = $this->commonVars();

        $vars = array_merge($vars, [
            'uploadUrl' => $this->uploadUrl,
        ]);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }
}
