<?php

namespace tpext\builder\traits\actions;

/**
 * 禁用/启用
 */

trait HasEnable
{
    public function enable()
    {
        $ids = input('post.ids', '');
        $ids = array_filter(explode(',', $ids), 'strlen');
        if (empty($ids)) {
            $this->error('参数有误');
        }
        $res = 0;
        foreach ($ids as $id) {
            if ($this->dataModel->where([$this->dataModel->getPk() => $id])->update([$this->enableField => 1])) {
                $res += 1;
            }
        }
        if ($res) {
            $this->success('成功操作' . $res . '条数据');
        } else {
            $this->error('操作失败');
        }
    }

    public function disable()
    {
        $ids = input('post.ids', '');
        $ids = array_filter(explode(',', $ids), 'strlen');
        if (empty($ids)) {
            $this->error('参数有误');
        }
        $res = 0;
        foreach ($ids as $id) {
            if ($this->dataModel->where([$this->dataModel->getPk() => $id])->update([$this->enableField => 0])) {
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
