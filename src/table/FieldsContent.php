<?php

namespace tpext\builder\table;

use think\Model;
use tpext\builder\common\Module;
use tpext\builder\common\Table;
use tpext\builder\displayer\Field;
use tpext\builder\inface\Renderable;

class FieldsContent extends TWrapper implements Renderable
{
    protected $view = 'fieldscontent';

    protected $cols = [];

    protected $data = [];

    /**
     * Undocumented variable
     *
     * @var Table
     */
    protected $table;

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function beforRender()
    {
        foreach ($this->cols as $col) {

            if (!$col instanceof TColumn) {
                $col->fill($this->data);
                $col->beforRender();
                continue;
            }

            $col->beforRender();
            $col->getDisplayer()
                ->showLabel(false)
                ->size(0, 0)
                ->value('');

            $col->fill($this->data);
        }
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param TColumn|Field $col
     * @return $this
     */
    public function addCol($col)
    {
        $this->cols[] = $col;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getCols()
    {
        return $this->cols;
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
     * @param array|Model $data
     * @return $this
     */
    public function fill($data = [])
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function value($val)
    {
        if (is_array($val)) {
            $this->data = $val;
        } else {
            $this->data = [];
        }
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function extKey($val)
    {
        foreach ($this->cols as $col) {
            if (!$col instanceof TColumn) {
                continue;
            }

            $col->getDisplayer()->extKey($val);
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function clearScript()
    {
        foreach ($this->cols as $col) {
            if (!$col instanceof TColumn) {
                continue;
            }

            $col->getDisplayer()->clearScript();
        }
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array|Model
     */
    public function getData()
    {
        return $this->data;
    }

    public function render()
    {
        $template = Module::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'table', $this->view . '.html']);

        $viewshow = view($template);

        $vars = [
            'cols' => $this->cols,
        ];

        return $viewshow->assign($vars)->getContent();
    }

    public function __call($name, $arguments)
    {
        $count = count($arguments);

        if ($name == 'fields') {
            throw new \UnexpectedValueException('[fields]不能再包含[fields]');
        }

        if ($count > 0 && static::isDisplayer($name)) {

            $col = new TColumn($arguments[0], $count > 1 ? $arguments[1] : '', $count > 2 ? $arguments[2] : 0);

            $this->col[$arguments[0]] = $col;

            return $col->$name($arguments[0], $col->getLabel());
        }

        throw new \UnexpectedValueException('未知调用:' . $name);
    }
}
