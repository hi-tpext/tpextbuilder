<?php

namespace tpext\builder\displayer;

class SwitchBtn extends Field
{
    protected $view = 'switchbtn';

    protected $class = 'switch-solid switch-primary';

    protected $checked = '';

    protected $pair = [1, 0];

    /**
     * Undocumented function
     * @example 1 [1,0] / ['yes','no'] ...
     * @param array $val
     * @return $this
     */
    public function pair($val)
    {
        if (count($val) == 2) {
            $this->pair = $val;
        }

        return $this;
    }

    protected function boxScript()
    {
        $inputId = $this->getId();

        $script = <<<EOT

        $('#{$inputId}').val($('#{$inputId}-box').is(':checked') ? '{$this->pair[0]}' : '{$this->pair[1]}');

        $('#{$inputId}-box').on('change', function(){
            $('#{$inputId}').val($('#{$inputId}-box').is(':checked') ? '{$this->pair[0]}' : '{$this->pair[1]}');
        });

EOT;
        $this->script[] = $script;

        return $script;
    }

    public function render()
    {
        $vars = $this->commonVars();

        if (!($this->value === '' || $this->value === null)) {
            $this->checked = $this->value;
        } else {
            $this->checked = $this->default;
        }

        $vars = array_merge($vars, [
            'checked' => $this->checked,
        ]);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }

    public function beforRender()
    {
        $this->boxScript();

        return parent::beforRender();
    }
}
