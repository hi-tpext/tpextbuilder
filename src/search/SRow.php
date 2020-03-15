<?php

namespace tpext\builder\search;

use tpext\builder\common\Renderable;
use tpext\builder\common\Search;
use tpext\builder\traits\HasDom;
use tpext\builder\traits\HasRow;

class SRow extends SWapper implements Renderable
{
    use HasDom;
    use HasRow;

    /**
     * Undocumented variable
     *
     * @var Search
     */
    protected $search;

    public function __construct($name, $label = '', $colSize = 3, $colClass = '', $colAttr = '')
    {
        if (empty($label)) {
            $label = ucfirst($name);
        }

        $this->name = $name;
        $this->label = $label;
        $this->cloSize = $colSize;
        $this->class = $colClass;
        $this->attr = $colAttr;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param Search $val
     * @return $this
     */
    public function setSearch($val)
    {
        $this->search = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return Search
     */
    public function getForm()
    {
        return $this->search;
    }

    public function __call($name, $arguments)
    {
        if (static::isDisplayer($name)) {

            $class = static::$displayerMap[$name];

            return $this->createDisplayer($class, $arguments);
        }

        throw new \UnexpectedValueException('未知调用:' . $name);
    }
}
