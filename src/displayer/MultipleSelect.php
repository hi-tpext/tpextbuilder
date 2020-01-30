<?php

namespace tpext\builder\displayer;

class MultipleSelect extends Checkbox
{
    protected $view = 'multipleselect';

    protected $js = [
        '/assets/tpextbuilder/js/select2/select2.min.js',
        '/assets/tpextbuilder/js/select2/i18n/zh-CN.js',
    ];

    protected $css = [
        '/assets/tpextbuilder/js/select2/select2.min.css',
    ];

    protected $attr = 'size="5"';

    protected $group = false;

    protected $select2 = true;

    protected $select2Options = [
        'placeholder' => '请选择',
        'allowClear' => true,
    ];

    /**
     * Undocumented function
     *
     * @param array $options
     * @return void
     */
    public function select2Options($options)
    {
        $this->select2Options = $options;
    }

    /**
     * Undocumented function
     *
     * @param boolean $use
     * @return void
     */
    public function select2($use)
    {
        $this->select2 = $use;
    }

    public function render()
    {
        $vars = $this->commonVars();

        if (!empty($this->value)) {
            $this->checked = $this->value;
        } else {
            $this->checked = $this->default;
        }

        foreach ($this->options as $option) {

            if (isset($option['options']) && isset($option['label'])) {
                $this->group = true;
                break;
            }
        }

        $vars = array_merge($vars, [
            'checked' => $this->checked,
            'group' => $this->group,
        ]);

        $config = [];

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->config($config)->getContent();
    }
}
