<?php

namespace tpext\builder\toolbar;

use tpext\builder\toolbar\LinkBtn;

class ActionBtn extends LinkBtn
{
    protected $view = 'actionbtn';

    protected $mapClass = [];

    protected $postRowid = '';

    protected $extClass = '';

    protected $data = [];

    protected $dataid = 0;

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

        $this->data = $data;

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

    public function parseMapClass($data)
    {
        $this->extClass = '';
        foreach ($this->mapClass as $class => $check) {
            if (isset($data[$check]) && $data[$check]) {
                $this->extClass .= ' ' . $class;
            }
        }
    }

    /**
     * Undocumented function
     *
     * @param array $mapData
     * @return $this
     */
    public function mapClass($mapData)
    {
        if (!empty($mapData) && isset($mapData[$this->name])) {
            $this->mapClass = $mapData[$this->name];
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

    protected function postRowidScript()
    {
        $script = '';
        $class = 'action-' . $this->name;

        $script = <<<EOT

        tpextbuilder.postRowid('{$class}', '{$this->postRowid}', {$this->confirm});

EOT;
        $this->script[] = $script;

        return $script;
    }

    public function beforRender()
    {
        if ($this->postRowid) {

            $this->postRowidScript();
        }

        $this->parseMapClass($this->data);

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
            'class' => $vars['class'] . $this->extClass,
            'href' => empty($this->__href__) ? $this->href : $this->__href__,
            'attr' => $this->attr,
            'useLayer' => $this->useLayer,
            'dataid' => $this->dataid,
        ]);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }
}
