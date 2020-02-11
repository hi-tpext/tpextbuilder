<?php

namespace tpext\builder\toolbar;

class LinkBtn extends Bar
{
    protected $view = 'linkbtn';

    protected $class = 'btn-default';

    protected $icon = '';

    protected $href = 'javascript:;';

    protected $postChecked = '';

    protected $confirm = true;

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function icon($val)
    {
        $this->icon = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function href($val)
    {
        $this->icon = $val;
        return $this;
    }

    public function postChecked($url, $confirm = true)
    {
        $this->postChecked = $url;
        $this->confirm = $confirm;
    }

    protected function postCheckedScript()
    {
        $script = '';
        $inputId = $this->getId();

        $script = <<<EOT

        tpextbuilder.postChecked('{$inputId}', '{$this->postChecked}', {$this->confirm});

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

        $vars = array_merge($vars, [
            'icon' => $this->icon,
            'href' => $this->href,
        ]);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }
}
