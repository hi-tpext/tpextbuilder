<?php

namespace tpext\builder\logic;

use tpext\builder\inface\Storage;

class LocalStorage implements Storage
{
    /**
     * Undocumented function
     *
     * @param Attachment $attachment
     * @return string url
     */
    public function process($attachment)
    {
        $attachment['url'] = $attachment['url'] . '?id=' . $attachment['id'];
        $attachment->save();

        return $attachment['url'];
    }
}
