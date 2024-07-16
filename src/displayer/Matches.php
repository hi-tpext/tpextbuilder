<?php

namespace tpext\builder\displayer;

use tpext\builder\traits\HasOptions;

class Matches extends Raw
{
    use HasOptions;

    protected $view = 'matche';

    protected $isInput = false;

    protected $separator = '、';

    protected $checked = '';

    /**
     * Undocumented function
     * ','
     * @param string $val
     * @return $this
     */
    public function separator($val = '、')
    {
        $this->separator = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $fieldType
     * @return $this
     */
    public function created($fieldType = '')
    {
        parent::created($fieldType);
        $this->separator = __blang('bilder_default_separator');
        return $this;
    }

    public function beforRender()
    {
        $this->checked = !($this->value === '' || $this->value === null || $this->value === []) ? $this->value : $this->default;
        return parent::beforRender();
    }

    public function renderValue()
    {
        $this->value = !($this->value === '' || $this->value === null || $this->value === []) ? $this->value : $this->default;

        $values = is_array($this->value) ? $this->value : explode(',', $this->value);
        $texts = [];

        foreach ($values as $value) {
            if (isset($this->options[$value])) {
                $texts[] = $this->options[$value];
            }
        }

        $this->value = implode($this->separator, $texts);

        return parent::renderValue();
    }

    public function customVars()
    {
        $this->checked = is_array($this->checked) ? implode(',', $this->checked) : (string)$this->checked;

        return array_merge(parent::customVars(), [
            'checked' => $this->checked,
        ]);
    }
}
