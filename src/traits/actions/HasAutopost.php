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
        $this->checkToken();

        $id = input('post.id/d', '');
        $name = input('post.name', '');
        $value = input('post.value', '');

        if (empty($id) || empty($name)) {
            $this->error('参数有误');
        }

        if (!empty($this->postAllowFields) && !in_array($name, $this->postAllowFields)) {
            $this->error('不允许的操作');
        }

        //单独修改一个字段，好多字段是未设置的，处理模型事件容易出错。不触发模型事件，不触发[update_time]修改
        $res = $this->dataModel->where([$this->getPk() => $id])->update([$name => $value]);

        if ($res) {
            $this->success('修改成功');
        } else {
            $this->error('修改失败，或无更改');
        }
    }
}
