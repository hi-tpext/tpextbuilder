<?php

namespace tpext\builder\search;

use tpext\builder\common\Renderable;
use tpext\builder\traits\HasDom;
use tpext\builder\traits\HasRow;

class Row extends SWapper implements Renderable
{
    use HasDom;
    use HasRow;

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

        $this->createDisplayer(\tpext\builder\displayer\Field::class, [$name, $label]);

        return $this;
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
