<?php

namespace tpext\builder\toolbar;

use think\Model;
use tpext\builder\common\Builder;

class Actions extends DropdownBtns
{
    protected $mapClass = [];

    protected $data = [];

    protected $dataId = 0;

    protected $initPostRowidScript = false;

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function dataId($val)
    {
        $this->dataId = $val;
        $this->addGroupAttr('data-id="' . $val . '"');
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|Model $data
     * @return $this
     */
    public function parseUrl($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $mapData
     * @return $this
     */
    public function mapClass($mapData)
    {
        return $this;
    }

    protected function postRowidScript()
    {
        if ($this->initPostRowidScript) {
            return '';
        }

        $actions = [];
        $confirms = [];

        foreach ($this->items as $url => $data) {
            if (stripos($url, '/') === false) {
                $url = url($url);
            }
            if (!Builder::checkUrl($url)) {
                continue;
            }
            if (is_string($data)) {
                $actions[$url] = $data;
                $confirms[$url] = '1';
            } else {
                if (isset($actions[$url]) && isset($actions[$url]['init']) && $actions[$url]['init']) {
                    continue;
                }
                $actions[$url]['init'] = 1;
                $actions[$url]['label'] = $data[0];
                $actions[$url]['class'] = isset($data[1]) ? $data[1] : '';
                $actions[$url]['icon'] = isset($data[2]) ? $data[2] : '';
                $actions[$url]['attr'] = isset($data[3]) ? $data[3] : '';
                $confirms[$url] = isset($data[4]) ? $data[4] : '1';
            }
        }

        if (empty($actions)) {
            return '';
        }

        $this->items = $actions;

        $script = '';
        $class = 'dropdown-actions';

        $this->addGroupClass($class);

        $confirms = json_encode($confirms, JSON_UNESCAPED_UNICODE);

        $script = <<<EOT

        tpextbuilder.postActionsRowid('{$class}', {$confirms});

EOT;
        $this->initPostRowidScript = true;

        $this->script[] = $script;

        return $script;
    }

    public function beforRender()
    {
        $this->postRowidScript();

        return parent::beforRender();
    }
}
