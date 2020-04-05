<?php

namespace tpext\builder\traits\actions;

/**
 * 字段编辑
 */

trait HasAutopost
{
    public function autopost()
    {
        $id = input('post.id/d', '');
        $name = input('post.name', '');
        $value = input('post.value', '');

        if (empty($id) || empty($name)) {
            $this->error('参数有误');
        }
        if (!empty($this->postAllowFields) && !in_array($name, $this->postAllowFields)) {
            $this->error('不允许的操作');
        }
        $res = $this->dataModel->where([$this->dataModel->getPk() => $id])->update([$name => $value]);

        if ($res) {
            $this->success('修改成功');
        } else {
            $this->error('修改失败，或无更改');
        }
    }
}
