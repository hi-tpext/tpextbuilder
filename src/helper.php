<?php

use tpext\common\ExtLoader;

$classMap = [
    'tpext\\builder\\common\\Module'
];

ExtLoader::addClassMap($classMap);

ExtLoader::watch('app_end', function () {
    \tpext\builder\common\Builder::destroyInstance();
}, false, '权限验证');
