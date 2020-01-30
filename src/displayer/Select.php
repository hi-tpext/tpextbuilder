<?php

namespace tpext\builder\displayer;

class Select extends Radio
{
    protected $view = 'select';

    protected $js = [
        '/assets/tpextbuilder/js/select2/select2.min.js',
        '/assets/tpextbuilder/js/select2/i18n/zh-CN.js',
    ];

    protected $css = [
        '/assets/tpextbuilder/js/select2/select2.min.css',
    ];

    protected $attr = 'size="1"';

    protected $emptyTip = true;

    protected $group = false;

    protected $select2 = true;

    protected $select2Options = [];

    /**
     * Undocumented function
     *
     * @param boolean $use
     * @return void
     */
    public function emptyTip($show)
    {
        $this->emptyTip = $show;
    }

    /**
     * Undocumented function
     *
     * @param boolean $show
     * @return void
     */
    public function select2($use)
    {
        $this->select2 = $use;
    }

    public function dataUrl($url,$options=['delay'=>250,'key'=>'id','text'=>'text'],$loadmore=true)
    {
        $this->select2Options['ajax'] = [
            'url' =>$url,
            'options' => $options,
            'loadmore' => $loadmore
        ];
    }

    /**
     * Undocumented function
     *
     * @param array $options
     * @return void
     */
    public function select2Options($options)
    {
        $this->select2Options = array_merge($this->select2Options,$options);
    }

    protected function select2Script()
    {
        if(isset(''))
        $script = <<<EOT
        var fields = '$fieldsStr'.split('.');
        var urls = '$urlsStr'.split('^');
        
        var refreshOptions = function(url, target) {
            $.get(url).then(function(data) {
                target.find("option").remove();
                $(target).select2({
                    placeholder: $placeholder,
                    allowClear: $allowClear,        
                    data: $.map(data, function (d) {
                        d.id = d.$idField;
                        d.text = d.$textField;
                        return d;
                    })
                }).trigger('change');
            });
        };
        EOT;

        return $script;
    }

    public function render()
    {
        if($this->select2)
        {
            $this->script[] = 
        }
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
            'emptyTip' => $this->emptyTip,
            'group' => $this->group,
        ]);

        $config = [];

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->config($config)->getContent();
    }
}
