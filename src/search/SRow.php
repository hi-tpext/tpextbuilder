<?php

namespace tpext\builder\search;

use tpext\builder\common\Search;
use tpext\builder\inface\Renderable;
use tpext\builder\traits\HasDom;
use tpext\builder\traits\HasRow;

class SRow extends SWrapper implements Renderable
{
    use HasDom;
    use HasRow;

    protected $filter = '';

    /**
     * Undocumented variable
     *
     * @var Search
     */
    protected $form;

    public function __construct($name, $label = '', $colSize = 2, $filter = '')
    {
        $this->name = trim($name);
        if (empty($label) && !empty($this->name)) {
            $label = lang(ucfirst($this->name));
        }

        $this->label = $label;
        $this->cloSize = $colSize;
        $this->filter = $filter;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param mixed $filter
     * @return $this
     */
    public function filter($filter)
    {
        $this->filter = $filter;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return mixed
     */
    public function getFilter()
    {
        return $this->filter ?: 'eq';
    }

    /**
     * Undocumented function
     *
     * @param Search $val
     * @return $this
     */
    public function setForm($val)
    {
        $this->form = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return Search
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Undocumented function
     *
     * @return int|string
     */
    public function getColSizeClass()
    {
        $cloSizeClass = $this->cloSize;

        if (is_int($cloSizeClass)) {
            $col = $cloSizeClass;
            if ($col <= 3) {
                $cloSizeClass .= " col-lg-{$col} col-sm-4 col-xs-6";
            } else if ($col <= 4) {
                $cloSizeClass .= " col-lg-{$col} col-sm-6 col-xs-12";
            } else {
                $cloSizeClass .= " col-lg-{$col} col-sm-12 col-xs-12";
            }
        } else {
            if (preg_match('/^(\d{1,2})\s+.*/', $cloSizeClass, $mch)) {
                $col = $mch[1];
                if ($col <= 3) {
                    $cloSizeClass .= " col-lg-{$col}";
                    if (!strstr($cloSizeClass, 'col-sm-')) {
                        $cloSizeClass .= ' col-sm-4';
                    }
                    if (!strstr($cloSizeClass, 'col-xs-')) {
                        $cloSizeClass .= ' col-xs-6';
                    }
                } else if ($col <= 4) {
                    $cloSizeClass .= " col-lg-{$col}";
                    if (!strstr($cloSizeClass, 'col-sm-')) {
                        $cloSizeClass .= ' col-sm-6';
                    }
                    if (!strstr($cloSizeClass, 'col-xs-')) {
                        $cloSizeClass .= ' col-xs-12';
                    }
                } else {
                    $cloSizeClass .= " col-lg-{$col}";
                    if (!strstr($cloSizeClass, 'col-sm-')) {
                        $cloSizeClass .= ' col-sm-12';
                    }
                    if (!strstr($cloSizeClass, 'col-xs-')) {
                        $cloSizeClass .= ' col-xs-12';
                    }
                }
            } else {
                if (!strstr($cloSizeClass, 'col-lg-')) {
                    $cloSizeClass .= ' col-lg-2';
                }
                if (!strstr($cloSizeClass, 'col-sm-')) {
                    $cloSizeClass .= ' col-sm-6';
                }
                if (!strstr($cloSizeClass, 'col-xs-')) {
                    $cloSizeClass .= ' col-xs-12';
                }
            }
        }

        return $cloSizeClass;
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

            $class = static::$displayersMap[$name];

            return $this->createDisplayer($class, $arguments);
        }

        throw new \UnexpectedValueException('未知调用:' . $name);
    }
}
