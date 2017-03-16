<?php

namespace QQun\UEditor\Uploader;

use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

/**
 *
 *
 * trait UploadQiniu
 *
 * 七牛 上传 类
 *
 * @package QQun\UEditor\Uploader
 */
trait UploadQiniu
{
    /**
     * 获取文件路径
     * @return string
     */
    protected function getFilePath()
    {
        $fullName = $this->fullName;
        $fullName = ltrim($fullName, '/');
        return $fullName;
    }

    public function uploadQiniu($key, $content)
    {
        $upManager = new UploadManager();
        $auth = new Auth(config('Ueditor.core.qiniu.accessKey'), config('Ueditor.core.qiniu.secretKey'));
        $token = $auth->uploadToken(config('Ueditor.core.qiniu.bucket'));

        list($ret, $error) = $upManager->put($token, $key, $content);
        if ($error) {
            $this->stateInfo = $error->message();
        } else {
            //change $this->fullName ,return the url
            $url = rtrim(strtolower(config('Ueditor.core.qiniu.url')), '/');
            $fullName = ltrim($this->fullName, '/');
            $this->fullName = $url . '/' . $fullName;
            $this->stateInfo = $this->stateMap[0];
        }

        return true;
    }
}