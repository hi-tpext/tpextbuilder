<?php

namespace tpext\builder\traits\actions;

/**
 * 禁用/启用
 */

trait HasEnable
{
    public function enable()
    {
        $state = input('state');

        $ids = input('post.ids', '');
        $ids = array_filter(explode(',', $ids), 'strlen');
        if (empty($ids)) {
            $this->error('参数有误');
        }
        $res = 0;
        foreach ($ids as $id) {

            //单独修改一个字段，好多字段是未设置的，处理模型事件容易出错。不触发模型事件，不触发[update_time]修改
            if ($this->dataModel->where([$this->getPk() => $id])->update([$this->enableField => $state])) {
                $res += 1;
            }
        }
        if ($res) {
            $this->success('成功操作' . $res . '条数据');
        } else {
            $this->error('操作失败');
        }
    }
}
