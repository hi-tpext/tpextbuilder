<?php

namespace tpext\builder\common;

use tpext\common\Module as baseModule;

class Module extends baseModule
{
    protected $version = '1.0.1';

    protected $name = 'tpext.builder';

    protected $title = 'tpext ui生成';

    protected $description = '后台ui生成';

    protected $root = __DIR__ . '/../../';

    protected $assets = 'assets';

    protected $modules = [
        'admin' => ['upload', 'import'],
    ];

    public function moduleInit($info = [])
    {
        parent::moduleInit($info);

        return true;
    }
}
