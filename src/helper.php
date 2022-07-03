<?php

use tpext\common\ExtLoader;

$classMap = [
    'tpext\\builder\\common\\Module'
];

ExtLoader::addClassMap($classMap);

if (!function_exists('class_basename')) {
    /**
     * 获取类名(不包含命名空间)
     *
     * @param mixed $class 类名
     * @return string
     */
    function class_basename($class): string
    {
        $class = is_object($class) ? get_class($class) : $class;
        return basename(str_replace('\\', '/', $class));
    }
}