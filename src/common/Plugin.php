<?php

namespace tpext\builder\common;

use tpext\common\Plugin as basePlugin;

class Plugin extends basePlugin
{
    protected $name = 'tpext.builder';

    protected $title = 'tpextbuilder';

    protected $description = '后台ui生成';

    protected $__root__ = __DIR__ . '/../../';

    protected $assets = 'assets';

    public function pluginInit($info = [])
    {
        return parent::pluginInit($info);
    }
}
