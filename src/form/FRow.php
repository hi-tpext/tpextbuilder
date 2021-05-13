<?php

namespace tpext\builder\form;

use tpext\builder\common\Form;
use tpext\builder\inface\Renderable;
use tpext\builder\traits\HasDom;
use tpext\builder\traits\HasRow;
use think\facade\Lang;

class FRow extends FWrapper implements Renderable
{
    use HasDom;
    use HasRow;

    /**
     * Undocumented variable
     *
     * @var Form
     */
    protected $form;

    public function __construct($name, $label = '', $colSize = 12)
    {
        $this->name = trim($name);
        if (empty($label) && !empty($this->name)) {
            $label = Lang::get(ucfirst($this->name));
        }

        $this->label = $label;
        $this->cloSize = $colSize;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param Form $val
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
     * @return Form
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
            if ($col == 0) {
                //
            } else if ($col <= 3) {
                $cloSizeClass .= " col-lg-{$col} col-sm-4 col-xs-6";
            } else if ($col <= 4) {
                $cloSizeClass .= " col-lg-{$col} col-sm-6 col-xs-12";
            } else {
                $cloSizeClass .= " col-lg-{$col} col-sm-12 col-xs-12";
            }
        } else {
            if (preg_match('/^(\d{1,2})\s+.*/', $cloSizeClass, $mch)) {
                $col = $mch[1];
                if ($col == 0) {
                    //
                } else if ($col <= 3) {
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
