<?php

namespace tpext\builder\table;

use think\Model;
use tpext\builder\common\Module;
use tpext\builder\common\Table;
use tpext\builder\displayer\Field;
use tpext\builder\inface\Renderable;
use tpext\think\View;

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

            if (!($col instanceof TColumn)) {
                $col->fill($this->data);
                $col->beforRender();
                continue;
            }

            $displayer = $col->getDisplayer();

            $displayer->clearScript();

            $displayer
                ->value('')
                ->fill($this->data)
                ->showLabel(false)
                ->size('0', '0 col-lg-0 col-sm-0 col-xs-0 row-' . $displayer->getName() . '-td')
                ->beforRender();
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
        $this->cols[$col->getLabel()] = $col;
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
     * @param mixed ...$fields
     * @return $this
     */
    public function with(...$fields)
    {
        if (count($fields) && $fields[0] instanceof \Closure) {
            $fields[0]($this->table);
        }

        $this->table->fieldsEnd();
        return $this;
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
            if (!($col instanceof TColumn)) {
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
            if (!($col instanceof TColumn)) {
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

    /**
     * Undocumented function
     *
     * @return array
     */
    public function customVars()
    {
        return [];
    }

    public function render()
    {
        $template = Module::getInstance()->getViewsPath() . 'table' . DIRECTORY_SEPARATOR . $this->view . '.html';

        $viewshow = new View($template);

        $vars = [
            'cols' => $this->cols,
        ];

        $customVars = $this->customVars();

        if (!empty($customVars)) {
            $vars = array_merge($vars, $customVars);
        }

        return $viewshow->assign($vars)->getContent();
    }

    public function __call($name, $arguments)
    {
        $count = count($arguments);

        if ($name == 'fields') {
            throw new \UnexpectedValueException('[fields]不能再包含[fields]');
        }

        if ($count > 0 && static::isDisplayer($name)) {

            $col = TColumn::make($arguments[0], $count > 1 ? $arguments[1] : '', $count > 2 ? $arguments[2] : 0);

            $this->cols[$arguments[0]] = $col;

            return $col->$name($arguments[0], $col->getLabel());
        }

        throw new \UnexpectedValueException('未知调用:' . $name);
    }
}
