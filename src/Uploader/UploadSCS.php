<?php

namespace QQun\UEditor\Uploader;


use SCS;
use SCSException;
use SCSWrapper;

/**
 * 文件上传 for SinaCloudStorage
 * Class UploadSCS
 * @package QQun\UEditor\Uploader
 */
trait UploadSCS
{

    public function uploadSCS($path, $content)
    {
        $scs = config('Ueditor.core.scs');

        SCS::setAuth($scs['accessKey'], $scs['secretKey']);

        SCS::setExceptions(true);

//        SCS::putObjectFile($content,$scs['bucket'], $path);

        $result = '';
        try {
            $result = SCS::putObject($content, $scs['bucket'], $path, SCS::ACL_PUBLIC_READ,
                [], ['Content-Type' => 'text/plain']);  // 所有文件都固定设置为 text/plain 了
        } catch (SCSException $e) {
            $e->getMessage();
        }
        if ($result) {
            $this->stateInfo = $this->getStateInfo(0);
        } else {
            $this->stateInfo = $this->getStateInfo('ERROR_UNKNOWN');
        }

        $this->fullName = SCS::getAuthenticatedURL($scs['bucket'], $path, 86400 * 365 * 100);;


        return true;
    }

}