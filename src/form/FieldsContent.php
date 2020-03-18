<?php

namespace tpext\builder\form;

use think\Model;
use think\response\View as ViewShow;
use tpext\builder\common\Form;
use tpext\builder\common\Module;
use tpext\builder\common\Renderable;
use tpext\builder\displayer\Field;

class FieldsContent extends FWapper implements Renderable
{
    protected $rows = [];

    protected $data = [];

    /**
     * Undocumented variable
     *
     * @var Form
     */
    protected $form;

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function beforRender()
    {
        foreach ($this->rows as $row) {
            $row->fill($this->data);
            if (!$row instanceof FRow) {
                $row->beforRender();
                continue;
            }

            $displayer = $row->getDisplayer();

            if ($displayer->isRequired()) {
                $this->form->addJqValidatorRule($displayer->getName(), 'required', true);
            }

            $row->beforRender();
        }
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param FRow|Field|Fillable $row
     * @return $this
     */
    public function addRow($row)
    {
        $this->rows[] = $row;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getRows()
    {
        return $this->rows;
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
     * @param array|Model $data
     * @return $this
     */
    public function fill($data = [])
    {
        $this->data = $data;
        return $this;
    }

    public function render()
    {
        $template = Module::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'form', 'fieldscontent.html']);

        $viewshow = new ViewShow($template);

        $vars = [
            'rows' => $this->rows,
        ];

        return $viewshow->assign($vars)->getContent();
    }

    public function __call($name, $arguments)
    {
        $count = count($arguments);

        if ($count > 0 && static::isDisplayer($name)) {

            $row = new FRow($arguments[0], $count > 1 ? $arguments[1] : '', $count > 2 ? $arguments[2] : 12, $count > 3 ? $arguments[3] : '', $count > 4 ? $arguments[4] : '');

            $this->rows[] = $row;

            return $row->$name($arguments[0], $row->getLabel());
        }

        throw new \UnexpectedValueException('未知调用:' . $name);
    }
}
