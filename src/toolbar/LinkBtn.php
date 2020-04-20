<?php

namespace tpext\builder\toolbar;

class LinkBtn extends Bar
{
    protected $view = 'linkbtn';

    protected $postChecked = '';

    protected $confirm = true;

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getId()
    {
        return 'btn-' . $this->name . preg_replace('/[^\w\-]/', '', $this->extKey);
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @param boolean|string $confirm
     * @return $this
     */
    public function postChecked($url, $confirm = true)
    {
        $this->postChecked = $url;
        $this->confirm = $confirm;

        return $this;
    }

    protected function postCheckedScript()
    {
        $script = '';
        $inputId = $this->getId();

        if (empty($this->confirm)) {
            $this->confirm = '';
        }

        $script = <<<EOT

        tpextbuilder.postChecked('{$inputId}', '{$this->postChecked}', '{$this->confirm}');

EOT;
        $this->script[] = $script;

        return $script;
    }

    public function beforRender()
    {
        if ($this->postChecked) {

            $this->postCheckedScript();
        }

        return parent::beforRender();
    }

    /**
     * Undocumented function
     *
     * @return mixed
     */
    public function render()
    {
        $vars = $this->commonVars();

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }
}
