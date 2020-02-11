<?php

namespace tpext\builder\displayer;

class UEditor extends Field
{
    protected $view = 'ueditor';

    protected $js = [
        '/assets/tpextbuilder/js/ueditor/ueditor.config.js',
    ];

    protected $configJsPath = '/assets/tpextbuilder/js/ueditor/ueditor.all.min.js';

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
        $script = '';
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

        $this->editorScript();

        return parent::beforRender();
    }

    public function render()
    {
        if (empty($this->uploadUrl)) {
            
            $token = session('uploadtoken') ? session('uploadtoken') : md5('uploadtoken' . time() . uniqid());$token = md5('uploadtoken' . time() . uniqid());

            session('uploadtoken', $token);

            $this->uploadUrl = url('/tpextbuilder/admin/upload/ueditor', ['token' => $token]);
        }

        $vars = $this->commonVars();

        $vars = array_merge($vars, [
            'uploadUrl' => $this->uploadUrl,
        ]);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }
}
