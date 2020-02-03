<?php

namespace tpext\builder\displayer;

class Number extends Text
{
    protected $view = 'number';

    protected $js = [
        '/assets/tpextbuilder/js/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js',
    ];

    protected $css = [
        '/assets/tpextbuilder/js/bootstrap-touchspin/jquery.bootstrap-touchspin.css',
    ];

    protected $jsOptions = [
        'min' => 0,
        'max' => 99999,
        //'postfix' => '%',
        //'prefix' => '¥',
        'step' => 1,
        'verticalbuttons' => true,
        'initval' => 0,
    ];

    /**
     * Undocumented function
     *
     * @param array $options
     * @return void
     */
    public function jsOptions($options)
    {
        $this->jsOptions = array_merge($this->jsOptions, $options);
    }

    protected function NumberScript()
    {
        $script = '';
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
        $this->NumberScript();

        return parent::beforRender();
    }
}
