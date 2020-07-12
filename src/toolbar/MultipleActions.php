<?php

namespace tpext\builder\toolbar;

use tpext\builder\common\Builder;

class MultipleActions extends DropdownBtns
{
    /**
     * Undocumented function
     *
     * @param array $confirms
     * @return $this
     */
    public function postChecked($confirms)
    {
        $this->postChecked = true;
        $this->confirms = $confirms;

        return $this;
    }

    protected function postCheckedScript()
    {
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
        $inputId = $this->getId();

        $confirms = json_encode($confirms, JSON_UNESCAPED_UNICODE);

        $script = <<<EOT

        tpextbuilder.postActionsChecked('{$inputId}', {$confirms});

EOT;
        $this->script[] = $script;

        return $script;
    }

    public function beforRender()
    {
        $this->postCheckedScript();
        return parent::beforRender();
    }
}
