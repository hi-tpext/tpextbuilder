<?php

namespace tpext\builder\toolbar;

class DropdownBtns extends Bar
{
    protected $view = 'dropdownbtns';

    protected $items = [];

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getId()
    {
        return 'dropdown-' . $this->name . preg_replace('/\W/', '', $this->extKey);
    }

    /**
     * Undocumented function
     *
     * @param array $options
     * @return $this
     */
    public function jsOptions($options)
    {
        $this->jsOptions = array_merge($this->jsOptions, $options);
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $items
     * @return $this
     */
    public function items($items)
    {
        $this->items = $items;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return mixed
     */
    public function render()
    {
        $vars = $this->commonVars();

        $vars = array_merge($vars, [
            'items' => $this->items,
        ]);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }
}
