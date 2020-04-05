<?php

namespace tpext\builder\form;

use think\Collection;
use think\response\View as ViewShow;
use tpext\builder\common\Form;
use tpext\builder\common\Module;
use tpext\builder\displayer\Field;
use tpext\builder\form\FRow;
use tpext\builder\table\Actionbar;
use tpext\builder\traits\HasDom;

class ItemsContent extends FWapper
{
    use HasDom;

    protected $view = 'itemscontent';

    protected $headers = [];

    protected $cols = [];

    protected $data = [];

    protected $list = [];

    protected $script = [];

    protected $pk = 'id';

    protected $ids = [];

    protected $emptyText = "<span>暂无相关数据~</span>";

    protected $isInitData = false;

    protected $actionRowText = '操作';

    protected $cnaDelete = true;

    protected $canAdd = true;

    protected $name = '';

    protected $template = [];

    /**
     * Undocumented variable
     *
     * @var Actionbar
     */
    protected $actionbar = null;

    /**
     * Undocumented variable
     *
     * @var Form
     */
    protected $form;

    public function __construct()
    {
        $this->class = 'table-striped table-hover table-bordered';
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return void
     */
    public function name($val)
    {
        $this->name = $val;
    }

    /**
     * Undocumented function
     *
     * @param string $val
     * @return $this
     */
    public function actionRowText($val)
    {
        $this->actionRowText = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $name
     * @param FRow|Field|Fillable $row
     * @return $this
     */
    public function addCol($name, $col)
    {
        $this->headers[$name] = $col->getLabel();
        $this->cols[$name] = $col;
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
     * @param boolean $val
     * @return $this
     */
    public function cnaDelete($val)
    {
        $this->cnaDelete = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function hasAction()
    {
        return $this->cnaDelete;
    }

    /**
     * Undocumented function
     *
     * @param boolean $val
     * @return $this
     */
    public function canAdd($val)
    {
        $this->canAdd = $val;
        return $this;
    }

    /**
     * Undocumented function
     * 主键, 默认 为 'id'
     * @param string $val
     * @return $this
     */
    public function pk($val)
    {
        $this->pk = $val;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array|Collection $data
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
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Undocumented function
     *
     * @return $this
     */
    public function beforRender()
    {
        $this->initData();

        return $this;
    }

    protected function initData()
    {
        $this->list = [];

        $pk = $this->pk;

        $cols = array_keys($this->cols);

        foreach ($this->data as $key => $data) {

            if (isset($data[$pk])) {

                $this->ids[$key] = $data[$pk];
            } else {
                $this->ids[$key] = $key;
            }

            foreach ($cols as $col) {

                $colunm = $this->cols[$col];

                if (!$colunm instanceof FRow) {
                    continue;
                }

                $displayer = $colunm->getDisplayer();

                $displayer->clearScript();

                $displayer
                    ->fill($data)
                    ->extKey($key)
                    ->arrayName([$this->name . '[' . $this->ids[$key] . '][', ']'])
                    ->showLabel(false)
                    ->size(0, 0)
                    ->addClass('item-field ' . ($displayer->isRequired() ? ' item-field-required' : ''))
                    ->addAttr('data-label="' . $colunm->getLabel() . '"')
                    ->beforRender();

                if ($displayer->isRequired()) {
                    $this->headers[$col] = $displayer->getLabel() . '<strong title="必填" class="field-required">*</strong>';
                }

                $this->list[$key][$col] = [
                    'displayer' => $displayer,
                    'value' => $displayer->render(),
                    'attr' => $displayer->getAttrWithStyle(),
                    'wapper' => $colunm,
                ];
            }
        }

        foreach ($this->cols as $colunm) {
            if (!$colunm instanceof FRow) {
                continue;
            }
            $displayer = $colunm->getDisplayer();

            $displayer->clearScript();

            $isRequired = $displayer->isRequired();
            $displayer->required(false);

            $displayer
                ->extKey('')
                ->arrayName([$this->name . '[' . 'new' . '][', ']'])
                ->showLabel(false)
                ->value('')
                ->size(0, 0)
                ->addClass('item-field ' . ($isRequired ? ' item-field-required' : ''))
                ->addAttr('data-label="' . $colunm->getLabel() . '"')
                ->beforRender();

            $displayer->extKey('-no-init-script'); //模板的id改了，避免被初始化，添加以后再初始化

            $this->template[] = [
                'value' => $displayer->render(),
                'attr' => $displayer->getAttrWithStyle(),
                'wapper' => $colunm,
            ];

            $script = $displayer->getScript();

            $this->script = array_merge($this->script, $script);
        }

        $this->isInitData = true;
    }

    public function render()
    {
        $template = Module::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['src', 'view', 'form', $this->view . '.html']);

        $viewshow = new ViewShow($template);

        $vars = [
            'name' => $this->name,
            'class' => $this->class,
            'attr' => $this->getAttrWithStyle(),
            'headers' => $this->headers,
            'cols' => $this->cols,
            'list' => $this->list,
            'data' => $this->data,
            'emptyText' => $this->emptyText,
            'ids' => $this->ids,
            'cnaDelete' => $this->cnaDelete,
            'actionRowText' => $this->actionRowText,
            'canAdd' => $this->canAdd,
            'cols' => $this->cols,
            'script' => implode('', array_unique($this->script)),
            'template' => $this->template,
        ];

        return $viewshow->assign($vars)->getContent();
    }

    public function __call($name, $arguments)
    {
        $count = count($arguments);

        if ($count > 0 && static::isDisplayer($name)) {

            $row = new FRow($arguments[0], $count > 1 ? $arguments[1] : '', $count > 2 ? $arguments[2] : 1, $count > 3 ? $arguments[3] : '', $count > 4 ? $arguments[4] : '');

            $this->rows[] = $row;

            return $row->$name($arguments[0], $row->getLabel());
        }

        throw new \UnexpectedValueException('未知调用:' . $name);
    }
}
