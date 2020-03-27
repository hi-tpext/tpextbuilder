<?php

namespace tpext\builder\traits;

trait HasBuilder
{
    //基础
    use \tpext\builder\traits\actions\HasBase;
    //按需加载，避免暴露不必要的action

    //列表
    use \tpext\builder\traits\actions\HasIndex;
    //添加/修改
    use \tpext\builder\traits\actions\HasAdd;
    use \tpext\builder\traits\actions\HasEdit;
    //字段编辑
    use \tpext\builder\traits\actions\HasAutopost;
    //禁用/启用
    use \tpext\builder\traits\actions\HasEnable;
    //删除
    use \tpext\builder\traits\actions\HasDelete;
}
