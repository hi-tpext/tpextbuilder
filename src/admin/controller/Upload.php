<?php

namespace tpext\builder\admin\controller;

use think\Controller;
use tpext\builder\common\model\Attachment;
use tpext\builder\common\Module;
use tpext\builder\logic\Upload as UploadTool;
use tpext\builder\logic\WebUploader;

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
    public function upfiles($utype = '', $token = '', $driver = '')
    {
        if (empty($token)) {
            return json(
                ['info' => 'no token', 'picurl' => '']
            );
        }

        if (session('_csrf_token_') != $token) {
            return json(
                ['info' => 'token error', 'picurl' => '']
            );
            return;
        }

        switch ($utype) {
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

        $storageDriver = Module::config('storage_driver');

        if ($driver) {
            $driver = str_replace('-', '\\', $driver);
            if (class_exists($driver)) {
                $storageDriver = $driver;
            }
        }

        $storageDriver = empty($storageDriver) || !class_exists($storageDriver)
            ? \tpext\builder\logic\LocalStorage::class : $storageDriver;

        $driver = new $storageDriver;

        $_config['driver'] = $driver;

        $up = null;

        if ($utype == 'webuploader') {
            $up = new WebUploader($_config);
        } else {
            $up = new UploadTool($_config);
        }

        $newPath = $up->uploadFile($file_input_name);

        if ($newPath === false) {
            //var_dump($up->errorNumber);
            return json(
                ['status' => 500, 'info' => '上传失败-' . $up->errorInfo, 'class' => 'error']
            );
            // 失败跟成功同样的方式返回
        } else {
            switch ($utype) {
                case 'wangeditor':
                    return json(
                        ['url' => $newPath]
                    );
                    break;
                case 'editormd':
                    return json(
                        [
                            "success" => 1,
                            "message" => '上传成功',
                            "url" => $newPath,
                        ]
                    );
                    break;
                case 'dropzone':
                    return json(
                        ['status' => 200, 'info' => '上传成功', 'picurl' => $newPath]
                    );
                    break;
                case 'webuploader':
                    return json(
                        ['status' => 200, 'info' => '上传成功', 'class' => 'success', 'id' => rand(1, 9999), 'picurl' => $newPath]
                    );
                    break;
                case 'tinymce':
                    return json(
                        [
                            "location" => $newPath,
                        ]
                    );
                    break;
                case 'ckeditor':
                    return json(
                        [
                            "uploaded" => 1,
                            "fileName" => pathinfo($newPath)['filename'],
                            "url" => $newPath,
                        ]
                    );
                    break;
                default:
                    return json(
                        [
                            "status" => 1,
                            "info" => '上传成功',
                            "url" => $newPath,
                        ]
                    );
            }
        }
    }

    /**
     * Undocumented function
     *
     * @title ueditor上传相关
     * @return mixed
     */
    public function ueditor($token = '', $driver = '')
    {
        if (empty($token)) {
            exit('no token');
        }

        if (session('_csrf_token_') != $token) {
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
                return json($this->saveFile('images', $driver));
                break;

                /* 上传视频 */
            case 'uploadvideo':
                return json($this->saveFile('videos', $driver));
                break;

                /* 上传附件 */
            case 'uploadfile':
                return json($this->saveFile('files', $driver));
                break;

                /* 列出图片 */
            case 'listimage':
                return json($this->saveFile('listimage', $driver));
                break;

                /* 列出附件 */
            case 'listfile':
                return json($this->showFile('listfile', $config));
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
                return json(['state' => 'callback参数不合法']);
            }
        } else {
            return json($result);
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
            return json(['state' => 400, 'message' => '上传数据为空']);
        }

        $picurl = $this->base64_image_content($picdata, 'images');

        if ($picurl) {
            return json(['state' => 200, 'picurl' => $picurl]);
        } else {
            return json(['state' => 500, 'message' => '上传失败']);
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

    private function base64_image_content($base64_image_content, $dirName)
    {
        //匹配出图片的格式
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)) {
            $type = $result[2];
            if (!preg_match('/^(png|jpg|jpeg|bmp|gif|webpg)$/i', $type)) {
                return false;
            }

            $scriptName = $_SERVER['SCRIPT_FILENAME'];

            $fileByDate = Module::config('file_by_date');
            $storageDriver = Module::config('storage_driver');

            $storageDriver = empty($storageDriver) || !class_exists($storageDriver)
                ? \tpext\builder\logic\LocalStorage::class : $storageDriver;

            $driver = new $storageDriver;

            $date = '';

            if ($fileByDate == 2) {
                $date = date('Ymd');
            } else if ($fileByDate == 3) {
                $date = date('Y/m');
            } else if ($fileByDate == 4) {
                $date = date('Y/md');
            } else if ($fileByDate == 5) {
                $date = date('Ym/d');
            } else if ($fileByDate == 6) {
                $date = date('Y/m/d');
            } else {
                $date = date('Ym');
            }

            $path = realpath(dirname($scriptName)) . "/uploads/{$dirName}/" . $date . '/';

            if (!is_dir($path)) {
                //检查是否有该文件夹，如果没有就创建，并给予最高权限
                mkdir($path, 0755, true);
            }

            $newName = 'file' . md5(microtime(true)) . mt_rand(1000, 9999) . '.' . $type;

            if (file_put_contents($path . $newName, base64_decode(str_replace($result[1], '', $base64_image_content)))) {

                $url = "/uploads/{$dirName}/" . $date . '/' . $newName;
                $name = 'base64' . date('YmdHis');

                $attachment = new Attachment;

                $res = $attachment->save([
                    'name' => mb_substr($name, 0, 55),
                    'admin_id' => session('?admin_id') ? session('admin_id') : 0,
                    'user_id' => session('?user_id') ? session('user_id') : 0,
                    'mime' => mime_content_type($path . $newName),
                    'suffix' => $type,
                    'size' => filesize($path . $newName) / (1024 ** 2),
                    'sha1' => hash_file('sha1', $path . $newName),
                    'storage' => 'local',
                    'url' => $url,
                ]);

                if ($res) {
                    $url =  $driver->process($attachment);
                }

                return $url;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private function saveFile($type = '', $driver = '')
    {
        $file_input_name = 'upfile';

        $config = Module::getInstance()->getConfig();

        $_config['allowSuffix'] = explode(',', $config['allow_suffix']);
        $_config['maxSize'] = $config['max_size'] * 1024 * 1024;
        $_config['isRandName'] = $config['is_rand_name'];
        $_config['fileByDate'] = $config['file_by_date'];
        $_config['dirName'] = $type;

        $storageDriver = Module::config('storage_driver');

        if ($driver) {
            $driver = str_replace('-', '\\', $driver);
            if (class_exists($driver)) {
                $storageDriver = $driver;
            }
        }

        $storageDriver = empty($storageDriver) || !class_exists($storageDriver)
            ? \tpext\builder\logic\LocalStorage::class : $storageDriver;

        $driver = new $storageDriver;

        $_config['driver'] = $driver;

        $up = new UploadTool($_config);

        $newPath = $up->uploadFile($file_input_name);
        if ($newPath === false) {
            //var_dump($up->errorNumber);
            //echo json_encode(['status' => 500, 'info' => '上传失败，没有权限', 'class' => 'error']);
            // 失败跟成功同样的方式返回
            return [
                "state" => "", // 上传状态，上传成功时必须返回"SUCCESS"
                "url" => '', // 返回的地址
                "title" => $up->errorInfo,
            ];
        } else {

            return [
                "state" => "SUCCESS", // 上传状态，上传成功时必须返回"SUCCESS"
                "url" => $newPath, // 返回的地址
                "title" => $newPath, // 附件名
            ];
        }
    }

    private function catchFile()
    {
        // 假装抓取成功了
        return  [
            "state" => "SUCCESS", // 上传状态，上传成功时必须返回"SUCCESS"
            "url" => './upload/images/lyear_5de21f46cd8ba.jpg', // 返回的地址
            "title" => 'lyear_5de21f46cd8ba', // 附件名
        ];
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
            return array(
                "state" => "no match file",
                "list" => array(),
                "start" => $start,
                "total" => count($files),
            );
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

        return $result;
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
