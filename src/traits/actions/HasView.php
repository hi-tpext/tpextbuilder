<?php

namespace tpext\builder\traits\actions;

/**
 * 查看
 */

trait HasView
{
    public function view($id)
    {
        if (request()->isPost()) {
            $this->error('不允许的操作');
        } else {
            $builder = $this->builder($this->pageTitle, $this->viewText);
            $data = $this->dataModel->get($id);
            if (!$data) {
                return $builder->layer()->close(0, '数据不存在');
            }
            $form = $builder->form();
            $this->form = $form;

            $this->builForm(2, $data);

            $rows = $this->form->getRows();

            $form->fill($data);

            $this->turn($rows);

            $form->readonly();

            return $builder->render();
        }
    }

    private function turn($rows)
    {
        $displayer = null;

        foreach ($rows as $row) {

            if ($row instanceof \tpext\builder\common\Tab || $row instanceof \tpext\builder\form\Step) {

                $rows_ = $row->getRows();
                foreach ($rows_ as $row_) {
                    if ($row_['content'] instanceof \tpext\builder\form\FieldsContent) {
                        $rows_ = $row_['content']->getRows();
                        $this->turn($rows_);
                    }
                }

                continue;
            }

            if (!$row instanceof \tpext\builder\form\FRow) {
                continue;
            }

            $displayer = $row->getDisplayer();

            if ($displayer instanceof \tpext\builder\displayer\Items) {

                $content = $displayer->getContent();
                $cols = $content->getCols();
                $this->turn($cols);

            } else if ($displayer instanceof \tpext\builder\displayer\Fields) {
                $content = $displayer->getContent();
                $rows_ = $content->getRows();
                $this->turn($rows_);

            } else if ($displayer instanceof \tpext\builder\displayer\Password) {
                $row->show($displayer->getName(), $row->getLabel())->default('*********');
            } else if (
                $displayer instanceof \tpext\builder\displayer\Text
                || $displayer instanceof \tpext\builder\displayer\Tags
                || $displayer instanceof \tpext\builder\displayer\Number
                || $displayer instanceof \tpext\builder\displayer\Textarea) {

                $row->show($displayer->getName(), $row->getLabel())->value($displayer->renderValue())->default('-空-');
            } else if ($displayer instanceof \tpext\builder\displayer\Checkbox || $displayer instanceof \tpext\builder\displayer\MultipleSelect) {

                $row->matches($displayer->getName(), $row->getLabel())->options($displayer->getOptions())->value($displayer->renderValue());
            } else if ($displayer instanceof \tpext\builder\displayer\Radio) {

                $row->match($displayer->getName(), $row->getLabel())->options($displayer->getOptions())->value($displayer->renderValue());
            } else if ($displayer instanceof \tpext\builder\displayer\SwitchBtn) {

                $pair = $displayer->getPair();
                $options = [$pair[0] => '是', $pair[1] => '否'];
                $row->match($displayer->getName(), $row->getLabel())->options($options)->value($displayer->renderValue());
            } else if (
                !($displayer instanceof \tpext\builder\displayer\Raw
                    || $displayer instanceof \tpext\builder\displayer\MultipleFile
                    || $displayer instanceof \tpext\builder\displayer\Divider
                    || $displayer instanceof \tpext\builder\displayer\Html)) {

                $row->show($displayer->getName(), $row->getLabel())->value($displayer->renderValue())->default('-空-');
            }
        }
    }
}
