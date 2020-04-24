<?php

namespace tpext\builder\traits\actions;

/**
 * 禁用/启用
 */

trait HasEnable
{
    public function enable($state)
    {
        $ids = input('post.ids', '');
        $ids = array_filter(explode(',', $ids), 'strlen');
        if (empty($ids)) {
            $this->error('参数有误');
        }
        $res = 0;
        foreach ($ids as $id) {
            if ($this->dataModel->update([$this->enableField => $state], [$this->dataModel->getPk() => $id])) {
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
