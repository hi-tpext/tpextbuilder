<?php

namespace tpext\builder\toolbar;

class DropdownBtns extends Bar
{
    protected $view = 'dropdownbtns';

    protected $items = [];

    protected $groupClass = '';

    protected $groupAttr = '';

    protected $groupStyle = '';

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getId()
    {
        return 'dropdown-' . $this->name . preg_replace('/[^\w\-]/', '', $this->extKey);
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
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    public function isEmpty()
    {
        return empty($this->items);
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function groupClass($val)
    {
        $this->groupClass = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function groupAttr($val)
    {
        $this->groupAttr = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function groupStyle($val)
    {
        $this->groupStyle = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function addGroupClass($val)
    {
        $this->groupClass .= ' ' . $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function addGroupAttr($val)
    {
        $this->groupAttr .= ' ' . $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function addGroupStyle($val)
    {
        $this->groupStyle .= $val;
        return $this;
    }

    public function getGroupAttrWithStyle()
    {
        return $this->groupAttr . (empty($this->groupStyle) ? '' : ' style="' . $this->groupStyle . '"');
    }

    /**
     * Undocumented function
     *
     * @return mixed
     */
    public function render()
    {
        $vars = $this->commonVars();

        $actions = [];

        $items = $this->getItems();

        foreach ($items as $key => $it) {
            if (is_string($it)) {
                $it = ['label' => $it, 'url' => url($key)];
            }
            $data = array_merge(
                [
                    'key' => $key,
                    'label' => '',
                    'icon' => '',
                    'url' => '',
                    'attr' => '',
                    'class' => '',
                ]
                , $it);

            $actions[$key] = $data;
        }

        $vars = array_merge($vars, [
            'items' => $actions,
            'groupAttr' => $this->getGroupAttrWithStyle(),
            'groupClass' => $this->groupClass,
        ]);

        $viewshow = $this->getViewInstance();

        return $viewshow->assign($vars)->getContent();
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function dataId($val)
    {
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|Model $data
     * @return $this
     */
    public function parseUrl($data)
    {
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $data
     * @return $this
     */
    public function parseMapClass($data)
    {
        return $this;
    }
}
