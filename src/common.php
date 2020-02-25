<?php

use think\facade\Request;

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
