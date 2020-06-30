<?php

namespace tpext\builder\table;

use think\facade\Lang;
use tpext\builder\common\Table;
use tpext\builder\inface\Renderable;
use tpext\builder\traits\HasDom;
use tpext\builder\traits\HasRow;

class TColumn extends TWrapper implements Renderable
{
    use HasDom;
    use HasRow;

    /**
     * Undocumented variable
     *
     * @var Table
     */
    protected $table;

    public function __construct($name, $label = '', $colSize = 12)
    {
        $this->name = trim($name);
        if (empty($label)) {
            $label = Lang::get(ucfirst($this->name));
        }

        $this->label = $label;
        $this->cloSize = $colSize;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param Table $val
     * @return $this
     */
    public function setTable($val)
    {
        $this->table = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return Table
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Undocumented function
     *
     * @param array $data
     * @return $this
     */
    public function fill($data = [])
    {
        $this->displayer->fill($data);
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
