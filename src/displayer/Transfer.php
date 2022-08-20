<?php

namespace tpext\builder\displayer;

use tpext\builder\traits\HasOptions;
use tpext\builder\traits\HasWhen;

class Transfer extends Field
{
    use HasOptions;
    use HasWhen;

    protected $view = 'transfer';

    protected $default = [];

    protected $checked = [];

    protected $disabledOptions = [];

    protected $js = [
        '/assets/tpextbuilder/js/bootstrap-duallistbox/jquery.bootstrap-duallistbox.min.js',
    ];

    protected $css = [
        '/assets/tpextbuilder/js/bootstrap-duallistbox/bootstrap-duallistbox.min.css',
    ];

    protected $group = false;

    protected $isArrayValue = true;

    protected $jsOptions = [
        'nonSelectedListLabel' => '<span class="help-block">未选择的选项</span>',
        'selectedListLabel' => '<span class="help-block">已选择的选项</span>',
        'filterPlaceHolder' => '筛选',
        'moveSelectedLabel' => "添加",
        'moveAllLabel' => '添加所有',
        'removeSelectedLabel' => "移除",
        'removeAllLabel' => '移除所有',
        'infoText' => '共{0}项',
        'infoTextFiltered' => '搜索到{0}项 ,共{1}项',
        'infoTextEmpty' => '空',
        'filterTextClear' => '清空',
        'moveOnSelect' => true,
        'selectorMinimalHeight' => 100,
    ];

    /**
     * Undocumented function
     *
     * @param array|string $val
     * @return $this
     */
    public function default($val = [])
    {
        $this->default = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function placeholder($val)
    {
        $this->jsOptions['placeholder'] = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string|array $val
     * @return $this
     */
    public function disabledOptions($val)
    {
        $this->disabledOptions = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function dualListScript()
    {
        $script = '';

        $selectId = $this->getId();

        $configs = json_encode($this->jsOptions);

        $configs = substr($configs, 1, strlen($configs) - 2);

        $script = <<<EOT

        $("#{$selectId}").bootstrapDualListbox({
            {$configs}
        });

        $('i.glyphicon.glyphicon-arrow-right').addClass('mdi mdi-chevron-right');
        $('i.glyphicon.glyphicon-arrow-left').addClass('mdi mdi-chevron-left');

EOT;
        $this->script[] = $script;

        return $script;
    }

    protected function isGroup()
    {
        foreach ($this->options as $option) {

            if (isset($option['options']) && isset($option['label'])) {
                $this->group = true;
                break;
            }
        }

        return $this->group;
    }

    public function beforRender()
    {
        $this->dualListScript();
        $this->whenScript();

        return parent::beforRender();
    }

    public function render()
    {
        $vars = $this->commonVars();

        if (!($this->value === '' || $this->value === null || $this->value === [])) {
            $this->checked = is_array($this->value) ? $this->value : explode(',', $this->value);
        } else if (!($this->default === '' || $this->default === null || $this->default === [])) {
            $this->checked = is_array($this->default) ? $this->default : explode(',', $this->default);
        }

        $this->isGroup();

        $dataSelected = $this->checked;

        foreach ($this->checked as &$ck) {
            $ck = '-' . $ck;
        }

        if ($this->disabledOptions && !is_array($this->disabledOptions)) {
            $this->disabledOptions = explode(',', $this->disabledOptions);
        }
        foreach ($this->disabledOptions as &$di) {
            $di = '-' . $di;
        }

        unset($ck);

        $vars = array_merge($vars, [
            'checked' => $this->checked,
            'dataSelected' => implode(',', $dataSelected),
            'group' => $this->group,
            'options' => $this->options,
            'disabledOptions' => $this->disabledOptions,
        ]);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }
}
