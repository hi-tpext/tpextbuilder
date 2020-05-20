<?php

namespace tpext\builder\toolbar;

use tpext\builder\common\Builder;

class ActionBtn extends Bar
{
    protected $view = 'actionbtn';

    protected $mapClass = [];

    protected $postRowid = '';

    protected $extClass = '';

    protected $data = [];

    protected $dataid = 0;

    protected $confirm = true;

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

    /**
     * Undocumented function
     *
     * @param string $url
     * @param boolean|string $confirm
     * @return $this
     */
    public function postRowid($url, $confirm = true)
    {
        $this->postRowid = $url;
        $this->confirm = $confirm;

        return $this;
    }

    protected function postRowidScript()
    {
        $script = '';
        $class = 'action-' . $this->name;

        if (empty($this->confirm)) {
            $this->confirm = '';
        }

        $script = <<<EOT

        tpextbuilder.postRowid('{$class}', '{$this->postRowid}', '{$this->confirm}');

EOT;
        $this->script[] = $script;

        return $script;
    }

    public function beforRender()
    {
        if ($this->postRowid) {

            if (Builder::checkUrl($this->postRowid)) {
                $this->postRowidScript();;
            } else {
                $this->addClass('hidden disabled');
            }
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

        $vars = array_merge($vars, [
            'class' => $vars['class'] . $this->extClass,
            'dataid' => $this->dataid,
        ]);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }
}
