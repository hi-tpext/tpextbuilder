<?php

namespace tpext\builder\admin\controller;

use think\Controller;
use tpext\builder\common\Module;
use tpext\builder\logic\Upload as UploadTool;

/* 参照 Light-Year-Example 相关上传处理方式*/

/**
 * Undocumented class
 * @title 上传
 */
class Upload extends Controller
{
    /**
     * Undocumented function
     *
     * @title 上传文件
     * @return mixed
     */
    public function upfiles($type = '', $token = '')
    {
        if (empty($token)) {
            echo json_encode(['info' => 'no token', 'picurl' => '']);
            return;
        }

        if (session('uploadtoken') != $token) {
            echo json_encode(['info' => 'token error', 'picurl' => '']);
            return;
        }

        switch ($type) {
            case 'editormd':
                $file_input_name = 'editormd-image-file';
                break;
            case 'ckeditor':
                $file_input_name = 'upload';
                break;
            default:
                $file_input_name = 'file';
        }

        $config = Module::getInstance()->getConfig();

        $_config['allowSuffix'] = explode(',', $config['allow_suffix']);
        $_config['maxSize'] = $config['max_size'] * 1024 * 1024;
        $_config['isRandName'] = $config['is_rand_name'];
        $_config['fileByDate'] = $config['file_by_date'];

        $up = new UploadTool($_config);

        $newPath = $up->uploadFile($file_input_name);

        if ($newPath === false) {
            //var_dump($up->errorNumber);
            echo json_encode(['status' => 500, 'info' => '上传失败-' . $up->errorInfo, 'class' => 'error']);
            // 失败跟成功同样的方式返回
        } else {
            switch ($type) {
                case 'wangeditor':
                    echo json_encode(['url' => $newPath]);
                    break;
                case 'editormd':
                    echo json_encode([
                        "success" => 1,
                        "message" => '上传成功',
                        "url" => $newPath,
                    ]);
                    break;
                case 'dropzone':
                    echo json_encode(['status' => 200, 'info' => '上传成功', 'picurl' => $newPath]);
                    break;
                case 'webuploader':
                    echo json_encode(['status' => 200, 'info' => '上传成功', 'class' => 'success', 'id' => rand(1, 9999), 'picurl' => $newPath]);
                    break;
                case 'tinymce':
                    echo json_encode([
                        "location" => $newPath,
                    ]);
                    break;
                case 'ckeditor':
                    echo json_encode([
                        "uploaded" => 1,
                        "fileName" => pathinfo($newPath)['filename'],
                        "url" => $newPath,
                    ]);
                    break;
                default:
                    echo json_encode([
                        "status" => 1,
                        "info" => '上传成功',
                        "url" => $newPath,
                    ]);
            }
        }
    }

    /**
     * Undocumented function
     *
     * @title ueditor上传相关
     * @return mixed
     */
    public function ueditor($token = '')
    {
        if (empty($token)) {
            exit('no token');
        }

        if (session('uploadtoken') != $token) {
            exit('token error');
        }

        $scriptName = $_SERVER['SCRIPT_FILENAME'];

        $action = $_GET['action'];
        $config_file = realpath(dirname($scriptName)) . '/assets/builderueditor/config.json';
        $config = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents($config_file)), true);
        switch ($action) {
            /* 获取配置信息 */
            case 'config':
                $result = $config;
                break;

            /* 上传图片 */
            case 'uploadimage':
            /* 上传涂鸦 */
            case 'uploadscrawl':
                echo $this->saveFile('images');
                exit;
                break;

            /* 上传视频 */
            case 'uploadvideo':
                echo $this->saveFile('videos');
                exit;
                break;

            /* 上传附件 */
            case 'uploadfile':
                echo $this->saveFile('files');
                exit;
                break;

            /* 列出图片 */
            case 'listimage':
                echo $this->showFile('listimage', $config);
                exit;
                break;

            /* 列出附件 */
            case 'listfile':
                echo $this->showFile('listfile', $config);
                exit;
                break;

            /* 抓取远程附件 */
            case 'catchimage':
                $result = $this->catchFile();
                break;

            default:
                $result = ['state' => '请求地址出错'];
                break;
        }

        /* 输出结果 */
        if (isset($_GET["callback"])) {
            if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
                echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
            } else {
                echo json_encode(['state' => 'callback参数不合法']);
            }
        } else {
            echo json_encode($result);
        }
    }

    /**
     * Undocumented function
     *
     * @title base64上传
     * @return mixed
     */
    public function base64()
    {
        $picdata = $_POST['picdata'];

        if (empty($picdata)) {
            echo json_encode(['state' => 400, 'message' => '上传数据为空']);
            exit;
        }

        $scriptName = $_SERVER['SCRIPT_FILENAME'];

        $picurl = $this->base64_image_content($picdata, realpath(dirname($scriptName)) . '/uploads/images/' . date('Ym') . '/');
        if ($picurl) {
            echo json_encode(['state' => 200, 'picurl' => $picurl]);
            exit;
        } else {
            echo json_encode(['state' => 500, 'message' => '上传失败']);
            exit;
        }
    }

    /**
     * Undocumented function
     *
     * @title 文件缩略图
     * @return mixed
     */
    public function ext($type)
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
            header("Expires: " . date(DATE_RFC822, strtotime("+2day")));
            readfile($file);
            exit;
        }

    }

    private function base64_image_content($base64_image_content, $path)
    {
        //匹配出图片的格式
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)) {
            $type = $result[2];
            if (!preg_match('/^(png|jpg|jpeg|bmp|gif|webpg)$/i', $type)) {
                return false;
            }
            $new_file = $path . "/" . date('Ymd', time()) . "/";

            if (!file_exists($new_file)) {
                //检查是否有该文件夹，如果没有就创建，并给予最高权限
                mkdir($new_file, 0755, true);
            }

            $new_file = $new_file . md5(microtime(true)) . ".{$type}";
            if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))) {
                return '/' . $new_file;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private function saveFile($type = '')
    {
        $file_input_name = 'upfile';

        $config = Module::getInstance()->getConfig();

        $_config['allowSuffix'] = explode(',', $config['allow_suffix']);
        $_config['maxSize'] = $config['max_size'] * 1024 * 1024;
        $_config['isRandName'] = $config['is_rand_name'];
        $_config['fileByDate'] = $config['file_by_date'];
        $_config['dirName'] = $type;

        $up = new UploadTool($_config);

        $newPath = $up->uploadFile($file_input_name);
        if ($newPath === false) {
            //var_dump($up->errorNumber);
            //echo json_encode(['status' => 500, 'info' => '上传失败，没有权限', 'class' => 'error']);
            // 失败跟成功同样的方式返回
            return json_encode([
                "state" => "", // 上传状态，上传成功时必须返回"SUCCESS"
                "url" => '', // 返回的地址
                "title" => $up->errorInfo,
            ]);
        } else {

            return json_encode([
                "state" => "SUCCESS", // 上传状态，上传成功时必须返回"SUCCESS"
                "url" => $newPath, // 返回的地址
                "title" => $newPath, // 附件名
            ]);
        }
    }

    private function catchFile()
    {
        // 假装抓取成功了
        return json_encode([
            "state" => "SUCCESS", // 上传状态，上传成功时必须返回"SUCCESS"
            "url" => './upload/images/lyear_5de21f46cd8ba.jpg', // 返回的地址
            "title" => 'lyear_5de21f46cd8ba', // 附件名
        ]);
    }

    private function showFile($type = '', $config)
    {
        /* 判断类型 */
        switch ($type) {
            /* 列出附件 */
            case 'listfile':
                $allowFiles = $config['fileManagerAllowFiles'];
                $listSize = $config['fileManagerListSize'];
                $path = realpath('./upload/files/');
                break;

            /* 列出图片 */
            case 'listimage':
            default:
                $allowFiles = $config['imageManagerAllowFiles'];
                $listSize = $config['imageManagerListSize'];
                $path = realpath('./upload/images/');
        }
        $allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);

        /* 获取参数 */
        $size = isset($_GET['size']) ? htmlspecialchars($_GET['size']) : $listSize;
        $start = isset($_GET['start']) ? htmlspecialchars($_GET['start']) : 0;
        $end = $start + $size;

        /* 获取附件列表 */
        $files = $this->getfiles($path, $allowFiles);
        if (!count($files)) {
            return json_encode(array(
                "state" => "no match file",
                "list" => array(),
                "start" => $start,
                "total" => count($files),
            ));
        }

        /* 获取指定范围的列表 */
        $len = count($files);
        for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--) {
            $list[] = $files[$i];
        }

        /* 返回数据 */
        $result = array(
            "state" => "SUCCESS",
            "list" => $list,
            "start" => $start,
            "total" => count($files),
        );

        return json_encode($result);
    }

    private function getfiles($path = '', $allowFiles = '', &$files = array())
    {
        if (!is_dir($path)) {
            return null;
        }

        if (substr($path, strlen($path) - 1) != '/') {
            $path .= '/';
        }

        $handle = opendir($path);
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                $path2 = $path . $file;
                if (is_dir($path2)) {
                    $this->getfiles($path2, $allowFiles, $files);
                } else {
                    if (preg_match("/\.(" . $allowFiles . ")$/i", $file)) {
                        $files[] = array(
                            'url' => str_replace("\\", "/", substr($path2, strlen($_SERVER['DOCUMENT_ROOT']))),
                            'mtime' => filemtime($path2),
                        );
                    }
                }
            }
        }
        return $files;
    }
}
