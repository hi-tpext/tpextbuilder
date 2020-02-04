<?php

namespace tpext\builder\common;

use tpext\common\Module as baseModule;

class Module extends baseModule
{
    protected $name = 'tpext.builder.module';

    protected $__root__ = __DIR__ . '/../../';

    protected $assets = 'assets';

    protected $modules = [
        'admin' => ['upload'],
    ];

    public function moduleInit($info = [])
    {
        parent::moduleInit($info);

        return true;
    }
}
