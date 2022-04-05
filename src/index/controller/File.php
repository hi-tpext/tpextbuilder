<?php

namespace tpext\builder\index\controller;

use think\Controller;
use tpext\builder\common\Module;

/* 参照 Light-Year-Example 相关上传处理方式*/

/**
 * Undocumented class
 * @title 上传
 */
class File extends Controller
{
    /**
     * Undocumented function
     *
     * @title 文件缩略图
     * @return mixed
     */
    public function extimg($type)
    {
        $file = Module::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['assets', 'images', 'ext', $type . '.png']);
        if (!file_exists($file)) {
            $file = Module::getInstance()->getRoot() . implode(DIRECTORY_SEPARATOR, ['assets', 'images', 'ext', '0.png']);
        }

        ob_start();

        $gmt_mtime = gmdate('r', filemtime($file));
        $ETag = '"' . md5($gmt_mtime . $file) . '"';

        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $_SERVER['HTTP_IF_MODIFIED_SINCE'] === $gmt_mtime) {
            header('ETag: ' . $ETag, true, 304);
            exit;
        }

        if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] === $ETag) {
            header('ETag: ' . $ETag, true, 304);
            exit;
        } else {
            header('ETag: ' . $ETag);
            header("Content-type: image/png");
            header("Cache-Control: private, max-age=10800, pre-check=10800");
            header("Pragma: private");
            header("Expires: " . date(DATE_RFC822, strtotime("+7day")));
            readfile($file);
            exit;
        }
    }
}
