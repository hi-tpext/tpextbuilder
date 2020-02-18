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

    protected $postRowid = '';

    protected $confirm = true;

    protected $dataid = 0;

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
        return 'btn-' . $this->name . $this->tableRowKey;
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
     * @param array $data
     * @return $this
     */
    public function parse($data)
    {
        $this->__href__ = preg_replace('/__data\.pk__/', $this->dataid, $this->href);

        if (empty($data)) {
            return $this;
        }

        $ma = preg_match_all('/__data\.(\w+)__/', $this->__href__, $matches);

        if ($ma) {
            foreach ($matches as $match) {
                if (count($match) > 0) {
                    $key = $match[0];
                }

                if (isset($data[$key])) {
                    $this->__href__ = preg_replace('/__data\.' . $key . '__/', $data[$key], $this->__href__);
                }
            }
        }
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function dataid($val)
    {
        $this->dataid = $val;
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
        $class = $this->name;

        $script = <<<EOT

        tpextbuilder.postRowid('{$class}', '{$this->postRowid}', {$this->confirm});

EOT;
        $this->script[] = $script;

        return $script;
    }

    protected function useLayerScript()
    {
        $script = '';
        $class = $this->name;

        $script = <<<EOT

        tpextbuilder.useLayer('{$class}');

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

        if ($this->useLayer && !empty($this->href) && !preg_match('/javascript:.*/i', $this->href)) {
            $this->useLayerScript();
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
            'href' => empty($this->__href__) ? $this->href : $this->__href__,
            'dataid' => $this->dataid ? "data-id='$this->dataid'" : '',
        ]);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }
}
