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

    public function renderValue()
    {
        $values = explode(',', $this->value);
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
        $this->checked = is_array($this->value) ? implode(',', $this->value) : (string)$this->value;

        return array_merge(parent::customVars(), [
            'checked' => $this->checked,
        ]);
    }
}
