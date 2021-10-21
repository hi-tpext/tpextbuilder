<?php

namespace tpext\builder\table;

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

    protected $colAttr = [
        'sortable' => false,
        'hidden' => false,
    ];

    public function __construct($name, $label = '', $colSize = 12)
    {
        $this->name = trim($name);
        if (empty($label) && !empty($this->name)) {
            $label = lang(ucfirst($this->name));
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

    /**
     * Undocumented function
     *
     * @param array $arr
     * @return $this
     */
    public function colAttr($arr)
    {
        $this->colAttr = array_merge($this->colAttr, $arr);

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getColAttr()
    {
        return $this->colAttr;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function sortable($val = true)
    {
        $this->colAttr['sortable'] = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function hidden($val = true)
    {
        $this->colAttr['hidden'] = $val;
        return $this;
    }

    public function __call($name, $arguments)
    {
        if (static::isDisplayer($name)) {

            $class = static::$displayersMap[$name];

            return $this->createDisplayer($class, $arguments);
        }

        throw new \UnexpectedValueException('未知调用:' . $name);
    }
}
