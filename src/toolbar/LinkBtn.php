<?php

namespace tpext\builder\toolbar;

class LinkBtn extends Bar
{
    protected $view = 'linkbtn';

    protected $class = 'btn-default';

    protected $icon = '';

    protected $href = 'javascript:;';

    protected $postChecked = '';

    protected $postRowid = '';

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

    /**
     * Undocumented function
     *
     * @param string $url
     * @param boolean $confirm
     * @return $this
     */
    public function postChecked($url, $confirm = true)
    {
        $this->postChecked = $url;
        $this->confirm = $confirm;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @param boolean $confirm
     * @return $this
     */
    public function postRowid($url, $confirm = true)
    {
        $this->postRowid = $url;
        $this->confirm = $confirm;

        return $this;
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

    protected function postRowidScript()
    {
        $script = '';
        $inputId = $this->getId();

        $script = <<<EOT

        tpextbuilder.postRowid('{$inputId}', '{$this->postRowid}', {$this->confirm});

EOT;
        $this->script[] = $script;

        return $script;
    }

    public function beforRender()
    {
        if ($this->postChecked) {

            $this->postCheckedScript();

        } else if ($this->postRowid) {

            $this->postRowidScript();
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
