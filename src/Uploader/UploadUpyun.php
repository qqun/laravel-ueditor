<?php
namespace QQun\UEditor\Uploader;

/**
 *
 *
 * trait UploadUpyun
 *
 * UpYun 上传 类
 *
 * @package QQun\UEditor\Uploader
 */
trait UploadUpyun
{

    public function uploadUpyun($path, $content)
    {
        $up = config('Ueditor.core.upyun');
        $upYun = new \UpYun($up['bucket'], $up['username'], $up['password'], $up['endpoint'], $up['timeout']);
        $result = '';
        try {
            $result = $upYun->writeFile($path, $content);
        } catch (\Exception $e) {
            $e->getMessage();
        }

        if (is_array($result) || $result) {
            $this->stateInfo = $this->getStateInfo(0);
        } else {
            $this->stateInfo = $this->getStateInfo('ERROR_UNKNOWN');
        }
        $url = rtrim(strtolower($up['url']), '/');
        $fullName = ltrim($this->fullName, '/');
        $this->fullName = $url . '/' . $fullName;

        return true;
    }

}