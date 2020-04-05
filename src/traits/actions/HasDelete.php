<?php

namespace tpext\builder\traits\actions;

/**
 * 删除
 */

trait HasDelete
{
    public function delete()
    {
        $ids = input('post.ids', '');
        $ids = array_filter(explode(',', $ids), 'strlen');
        if (empty($ids)) {
            $this->error('参数有误');
        }
        $res = 0;
        foreach ($ids as $id) {
            if (!$this->canDel($id)) {
                continue;
            }
            if ($this->dataModel->destroy($id)) {
                $res += 1;
                $this->afterDel($id);
            }
        }

        if ($res) {
            $this->success('成功删除' . $res . '条数据');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * 判断是否可以删除 ，可使用模型的befroDelete事件替代
     *
     * @param [type] $id
     * @return boolean
     */
    protected function canDel($id)
    {
        if (!empty($this->delNotAllowed) && in_array($id, $this->delNotAllowed)) {
            return false;
        }
        // 其他逻辑
        return true;
    }

    /**
     * 删除以后，可使用模型的afterDelete事件替代
     *
     * @param [type] $id
     * @return boolean
     */
    protected function afterDel($id)
    {
        return true;
    }
}
