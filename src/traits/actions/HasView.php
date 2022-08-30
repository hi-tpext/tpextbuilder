<?php

namespace tpext\builder\traits\actions;

use tpext\builder\common\Tab;
use tpext\builder\displayer;
use tpext\builder\form\FieldsContent;
use tpext\builder\form\FRow;
use tpext\builder\form\Step;

define('FORM_VIEW', 2);
/**
 * 查看
 */

trait HasView
{
    public function view()
    {
        $id = input('id');

        if (request()->isGet()) {

            $builder = $this->builder($this->pageTitle, $this->viewText, 'view');

            $data = $this->dataModel->where($this->getPk(), $id)->find();
            if (!$data) {
                return $builder->layer()->close(0, '数据不存在');
            }

            $form = $builder->form();
            $this->form = $form;
            $this->isEdit = 2;
            $this->buildForm($this->isEdit, $data);
            $rows = $this->form->getRows();
            $form->fill($data);

            $this->turn($rows);
            $form->readonly();

            return $builder->render();
        }

        $this->error('不允许的操作');
    }

    private function turn($rows)
    {
        $displayer = null;

        $fieldName = '';

        foreach ($rows as $row) {

            if ($row instanceof Tab || $row instanceof Step) {

                $rows_ = $row->getRows();
                foreach ($rows_ as $row_) {
                    if ($row_['content'] instanceof FieldsContent) {
                        $rows_ = $row_['content']->getRows();
                        $this->turn($rows_);
                    }
                }

                continue;
            }

            if (!($row instanceof FRow)) {
                continue;
            }

            $displayer = $row->getDisplayer();

            $fieldName = $displayer->getName();

            if (
                $displayer instanceof displayer\Button || $displayer instanceof displayer\Show || $displayer instanceof displayer\Raw
                || $displayer instanceof displayer\Matche || $displayer instanceof displayer\Matches
                || $displayer instanceof displayer\Load || $displayer instanceof displayer\Loads  || $displayer instanceof displayer\Map
            ) {
                continue;
            } else if ($displayer instanceof displayer\Items) {

                $content = $displayer->getContent();
                $this->turn($content->getCols());
            } else if ($displayer instanceof displayer\Fields) {

                $content = $displayer->getContent();
                $this->turn($content->getRows());
            } else if ($displayer instanceof displayer\Password) {

                $row->show($fieldName, $row->getLabel())->default('*********');
            } else if (
                $displayer instanceof displayer\Text || $displayer instanceof displayer\Tags
                || $displayer instanceof displayer\Number || $displayer instanceof displayer\Textarea
            ) {

                $row->show($fieldName, $row->getLabel())->default('-空-');
            } else if ($displayer instanceof displayer\Select && $displayer->isAjax()) { // multipleSelect(ajax) / select(ajax)

                $displayer->whenScript(true);
                $ajax = $displayer->getAjax();

                if ($displayer instanceof displayer\multipleSelect) {
                    $row->loads($fieldName, $row->getLabel())->dataUrl($ajax['url'], $ajax['text']);
                } else {
                    $row->load($fieldName, $row->getLabel())->dataUrl($ajax['url'], $ajax['text']);
                }
            } else if (
                $displayer instanceof displayer\Checkbox || $displayer instanceof displayer\MultipleSelect
                || $displayer instanceof displayer\Transfer
            ) { // checkbox / multipleSelect(非ajax) / Transfer

                $displayer->whenScript(true);
                $row->matches($fieldName, $row->getLabel())->options($displayer->getOptions());
            } else if ($displayer instanceof displayer\Radio || $displayer instanceof displayer\Select) { // radio / select(非ajax)

                $displayer->whenScript(true);
                $row->match($fieldName, $row->getLabel())->options($displayer->getOptions());
            } else if ($displayer instanceof displayer\SwitchBtn) {

                $pair = $displayer->getPair();
                $options = [$pair[0] => '是', $pair[1] => '否'];
                $row->match($fieldName, $row->getLabel())->options($options);
            } else if (!($displayer instanceof displayer\MultipleFile
                || $displayer instanceof displayer\Divider || $displayer instanceof displayer\Html)) {

                $row->raw($fieldName, $row->getLabel())->default('-空-');
            }

            $size = $displayer->getSize();

            $row->getDisplayer()
                ->showLabel($displayer->isShowLabel())
                ->size($size[0], $size[1])
                ->help($displayer->getHelp());
        }
    }
}
