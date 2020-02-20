<?php

namespace tpext\builder\toolbar;

class LinkBtn extends Bar
{
    protected $view = 'linkbtn';

    protected $class = 'btn-default';

    protected $icon = '';

    protected $href = 'javascript:;';

    protected $__href__ = '';

    protected $postChecked = '';

    protected $confirm = true;

    protected $useLayer = false;

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function useLayer($val)
    {
        $this->useLayer = $val;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getId()
    {
        return 'btn-' . $this->name . preg_replace('/\W/', '', $this->tableRowKey);
    }

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
        $this->href = $val;
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

        $this->useLayer = $this->useLayer && !empty($this->href) && !preg_match('/javascript:.*/i', $this->href) && !preg_match('/^#.*/i', $this->href);

        $vars = array_merge($vars, [
            'icon' => $this->icon,
            'href' => empty($this->__href__) ? $this->href : $this->__href__,
            'attr' => $this->attr,
            'useLayer' => $this->useLayer,
        ]);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }
}
