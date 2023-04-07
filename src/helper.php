<?php

use tpext\common\ExtLoader;
use think\facade\Request;
use think\facade\Lang;

$classMap = [
    'tpext\\builder\\common\\Module'
];

ExtLoader::addClassMap($classMap);

if (!function_exists('csrf_token')) {
    /**
     * 生成表单令牌
     * @param string $name 令牌名称
     * @param mixed  $type 令牌生成方法
     * @return string
     */
    function csrf_token($name = '__token__', $type = 'md5')
    {
        $token = Request::token($name, $type);

        return $token;
    }
}

if (!function_exists('__blang')) {
    function __blang(string $name = null, array $vars = [], string $range = '')
    {
        return Lang::get($name, $vars, $range);
    }
}
