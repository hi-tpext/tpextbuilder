<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------
use think\facade\Request;
use tpext\common\ExtLoader;

$classMap = [
    'tpext\\builder\\common\\Module',
    'tpext\\builder\\common\\Plugin',
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
