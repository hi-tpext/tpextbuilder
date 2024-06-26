<?php

namespace tpext\builder\displayer;

use tpext\builder\traits\HasStorageDriver;
use tpext\builder\traits\HasImageDriver;

class UEditor extends Field
{
    use HasStorageDriver;
    use HasImageDriver;

    protected $view = 'ueditor';

    protected $minify = false;

    protected $js = [
        '/assets/builderueditor/ueditor.all.min.js',
    ];

    protected $configJsPath = '/assets/builderueditor/ueditor.config.js';

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
        $this->js = array_merge([$this->configJsPath], $this->js);

        if (!$this->readonly) {
            $this->editorScript();
        }

        return parent::beforRender();
    }

    public function render()
    {
        if (empty($this->uploadUrl)) {

            $token = $this->getCsrfToken();

            $this->uploadUrl = (string)url($this->getUploadUrl(), [
                'utype' => 'ueditor',
                'token' => $token,
                'driver' => $this->getStorageDriver(),
                'is_rand_name' => $this->isRandName(),
                'image_driver' => $this->getImageDriver(),
                'image_commonds' => $this->getImageCommands()
            ]);
        }

        $vars = $this->commonVars();

        $vars = array_merge($vars, [
            'uploadUrl' => $this->uploadUrl,
        ]);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }
}
