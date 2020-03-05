<?php

return [
    'allowSuffix' =>
    //
    "jpg,jpeg,gif,wbmp,webpg,png,bmp," .
    //
    "flv,swf,mkv,avi,rm,rmvb,mpeg,mpg,ogv,mov,wmv,mp4,webm," .
    //
    "ogg,mp3,wav,mid," .
    //
    "rar,zip,tar,gz,7z,bz2,cab,iso," .
    //
    "doc,docx,xls,xlsx,ppt,pptx,pdf,txt,md," .
    //
    "xml,json,"
    ,
    'maxSize' => 20,
    'isRandName' => 1,
    //配置描述
    '__config__' => [
        'allowSuffix' => ['type' => 'textarea', 'label' => '允许上传的文件后缀', 'size' => [2, 10], 'help' => '以英文,号分割'],
        'maxSize' => ['type' => 'number', 'label' => '上传文件大小限制(MB)'],
        'isRandName' => ['type' => 'radio', 'label' => '随机文件名', 'options' => [0 => '否', 1 => '是']],
    ],
];
