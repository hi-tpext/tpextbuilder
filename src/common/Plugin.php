<?php

namespace tpext\builder\common;

use tpext\common\Plugin as basePlugin;

class Plugin extends basePlugin
{
    protected $name = 'tpext.builder.plugin';

    protected $__root__ = __DIR__ . '/../../';

    public function pluginInit($info = [])
    {
        return parent::pluginInit($info);
    }
}
