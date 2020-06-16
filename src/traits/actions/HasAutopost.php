<?php

namespace tpext\builder\traits\actions;

/**
 * 字段编辑
 */

trait HasAutopost
{
    public function autopost()
    {
        return $this->_autopost();
    }

    protected function _autopost()
    {
        $id = input('post.id/d', '');
        $name = input('post.name', '');
        $value = input('post.value', '');

        if (empty($id) || empty($name)) {
            $this->error('参数有误');
        }

        if ($name == $this->enableField) {
            $value = $value ? 1 : 0;
        }

        if (!empty($this->postAllowFields) && !in_array($name, $this->postAllowFields)) {
            $this->error('不允许的操作');
        }

        $res = $this->dataModel->update([$name => $value], [$this->getPk() => $id]);

        if ($res) {
            $this->success('修改成功');
        } else {
            $this->error('修改失败，或无更改');
        }
    }
}
