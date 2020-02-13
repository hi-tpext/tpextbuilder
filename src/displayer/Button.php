<?php

namespace tpext\builder\displayer;

class Button extends Field
{
    protected $view = 'button';

    protected $type = 'button';

    protected $bottom = false;

    protected $size = [0, 12];

    protected $showLabel = false;

    protected $class = 'btn-default';

    protected $loading = false;

    public function created()
    {
        parent::created();

        if (in_array($this->name, ['submit', 'reset'])) {
            $this->type = $this->name;
        }
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return void
     */
    public function type($val)
    {
        $this->type = $val;
    }

    public function loading($val = true)
    {
        $this->loading = $val;
    }

    public function render()
    {
        if ($this->loading) {
            $this->class .= ' btn-loading';
        }

        $vars = $this->commonVars();

        $vars = array_merge($vars, [
            'type' => $this->type,
        ]);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();

        return parent::render();
    }
}
