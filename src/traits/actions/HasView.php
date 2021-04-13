<?php

namespace tpext\builder\traits\actions;

use tpext\builder\common\Tab;
use tpext\builder\displayer;
use tpext\builder\form\FieldsContent;
use tpext\builder\form\FRow;
use tpext\builder\form\Step;

/**
 * 查看
 */

trait HasView
{
    public function view($id)
    {
        if (request()->isGet()) {

            $builder = $this->builder($this->pageTitle, $this->viewText, 'view');

            $data = $this->dataModel->get($id);
            if (!$data) {
                return $builder->layer()->close(0, '数据不存在');
            }

            $form = $builder->form();
            $this->form = $form;

            $this->buildForm(2, $data);
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

            if (!$row instanceof FRow) {
                continue;
            }

            $displayer = $row->getDisplayer();

            $fieldName = $displayer->getName();

            if ($displayer instanceof displayer\Button || $displayer instanceof displayer\Show || $displayer instanceof displayer\Raw
                || $displayer instanceof displayer\Matche || $displayer instanceof displayer\Matches) {
                continue;
            } else if ($displayer instanceof displayer\Items) {

                $content = $displayer->getContent();
                $this->turn($content->getCols());

            } else if ($displayer instanceof displayer\Fields) {

                $content = $displayer->getContent();
                $this->turn($content->getRows());

            } else if ($displayer instanceof displayer\Password) {

                $row->show($fieldName, $row->getLabel())->default('*********');

            } else if ($displayer instanceof displayer\Text || $displayer instanceof displayer\Tags
                || $displayer instanceof displayer\Number || $displayer instanceof displayer\Textarea) {

                $row->show($fieldName, $row->getLabel())->value($displayer->renderValue())->default('-空-');
            } 
            else if ($displayer instanceof displayer\Select && $displayer->isAjax()) {// multipleSelect(ajax) / select(ajax)
                //
            }
            else if ($displayer instanceof displayer\Checkbox || $displayer instanceof displayer\MultipleSelect) { // checkbox / select(非ajax)

                $row->matches($fieldName, $row->getLabel())->options($displayer->getOptions())->value($displayer->renderValue());

            } else if ($displayer instanceof displayer\Radio || $displayer instanceof displayer\Select) { // radio / select(非ajax)

                $row->match($fieldName, $row->getLabel())->options($displayer->getOptions())->value($displayer->renderValue());

            } else if ($displayer instanceof displayer\SwitchBtn) {

                $pair = $displayer->getPair();
                $options = [$pair[0] => '是', $pair[1] => '否'];
                $row->match($fieldName, $row->getLabel())->options($options)->value($displayer->renderValue());

            } else if (!($displayer instanceof displayer\MultipleFile
                || $displayer instanceof displayer\Divider || $displayer instanceof displayer\Html)) {

                $row->raw($fieldName, $row->getLabel())->value($displayer->renderValue())->default('-空-');
            }

            $size = $displayer->getSize();
            $row->getDisplayer()->showLabel($displayer->isShowLabel())->size($size[0], $size[1]);
        }
    }
}
