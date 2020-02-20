<?php

namespace tpext\builder\common;

use tpext\common\Module as baseModule;

class Module extends baseModule
{
    protected $name = 'tpext.builder';

    protected $title = 'tpextui生成';

    protected $description = '后台ui生成';

    protected $__root__ = __DIR__ . '/../../';

    protected $modules = [
        'admin' => ['upload'],
    ];

    public function moduleInit($info = [])
    {
        parent::moduleInit($info);

        return true;
    }
}
