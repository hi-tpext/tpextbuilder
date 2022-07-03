<?php

namespace tpext\builder\displayer;

class Number extends Field
{
    protected $view = 'number';

    protected $rules = 'number';

    protected $size = [2, 3];

    protected $js = [
        '/assets/tpextbuilder/js/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js',
    ];

    protected $css = [
        '/assets/tpextbuilder/js/bootstrap-touchspin/jquery.bootstrap-touchspin.css',
    ];

    protected $placeholder = '';

    protected $jsOptions = [
        //'postfix' => '%',
        //'prefix' => '¥',
        'min' => 0,
        'max' => 9999999,
        'step' => 1,
        'verticalbuttons' => true,
        'initval' => 0,
    ];

    /**
     * Undocumented function
     *
     * @param int $val
     * @return $this
     */
    public function min($val)
    {
        $this->jsOptions['min'] = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param int $val
     * @return $this
     */
    public function max($val)
    {
        $this->jsOptions['max'] = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param int $val
     * @return $this
     */
    public function step($val)
    {
        $this->jsOptions['step'] = $val;
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
        $this->placeholder = $val;
        return $this;
    }

    protected function numberScript()
    {
        $inputId = $this->getId();

        $configs = json_encode($this->jsOptions);

        $configs = substr($configs, 1, strlen($configs) - 2);

        $script = <<<EOT

        $('#{$inputId}').TouchSpin({
            {$configs}
        });

EOT;
        $this->script[] = $script;

        return $script;
    }

    public function beforRender()
    {
        $this->numberScript();

        return parent::beforRender();
    }

    public function customVars()
    {
        return [
            'placeholder' => $this->placeholder,
        ];
    }
}
