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
        $confirms = [];
        $actions = [];

        foreach ($this->items as $key => $item) {
            if (is_string($item)) {
                $item = ['label' => $item];
            }
            if (!isset($item['url']) || empty($item['url'])) {
                if ($key == 'enable') {
                    $item['url'] = url('enable', ['state' => 1])->__toString();
                } else if ($key == 'disable') {
                    $item['url'] = url('enable', ['state' => 0])->__toString();
                } else {
                    $item['url'] = url($key)->__toString();
                }
            } else {
                if (stripos($item['url'], '/') === false) {
                    $item['url'] = url($item['url'])->__toString();
                }
            }
            if (!Builder::checkUrl($item['url'])) {
                continue;
            }
            $confirms[$item['url']] = isset($item['confirm']) ? $item['confirm'] : '1';
            $actions[$key] = $item;
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
