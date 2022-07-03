<?php

namespace tpext\builder\displayer;

class Button extends Field
{
    protected $view = 'button';

    protected $isInput = false;

    protected $type = 'button';

    protected $bottom = false;

    protected $size = [0, '12 col-lg-12 col-sm-12 col-xs-12'];

    protected $showLabel = false;

    protected $loading = false;

    public function created($fieldType = '')
    {
        parent::created($fieldType);

        if (in_array($this->name, ['submit', 'reset'])) {
            $this->type = $this->name;
        }
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function type($val)
    {
        $this->type = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function loading($val = true)
    {
        $this->loading = $val;
        return $this;
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
    }
}
