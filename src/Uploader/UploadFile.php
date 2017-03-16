<?php

namespace QQun\UEditor\Uploader;

/**
 *
 * Class UploadFile
 *
 * 文件/图像普通上传
 * @package QQun\UEditor\Uploader
 */
class UploadFile extends Upload
{
    use UploadQiniu,UploadUpyun;

    public function doUpload()
    {

        $file = $this->request->file($this->fileField);


        if (empty($file)) {
            $this->stateInfo = $this->getStateInfo("ERROR_FILE_NOT_FOUND");
            return false;
        }
        if (!$file->isValid()) {
            $this->stateInfo = $this->getStateInfo($file->getError());
            return false;

        }

        $this->file = $file;

        $this->oriName = $this->file->getClientOriginalName();

        $this->fileSize = $this->file->getSize();
        $this->fileType = $this->getFileExt();

        $this->fullName = $this->getFullName();


        $this->filePath = $this->getFilePath();

        $this->fileName = basename($this->filePath);


        //检查文件大小是否超出限制
        if (!$this->checkSize()) {
            $this->stateInfo = $this->getStateInfo("ERROR_SIZE_EXCEED");
            return false;
        }
        //检查是否不允许的文件格式
        if (!$this->checkType()) {
            $this->stateInfo = $this->getStateInfo("ERROR_TYPE_NOT_ALLOWED");
            return false;
        }

        if (config('Ueditor.core.mode') == self::LOCAL_MODEL) {
            try {
                $this->file->move(dirname($this->filePath), $this->fileName);

                $this->stateInfo = $this->stateMap[0];

            } catch (FileException $exception) {
                $this->stateInfo = $this->getStateInfo("ERROR_WRITE_CONTENT");
                return false;
            }

        } else if (config('Ueditor.core.mode') == self::QINIU_MODEL) {

            $content = file_get_contents($this->file->getPathname());
            return $this->uploadQiniu($this->filePath, $content);

        }else if(config('Ueditor.core.mode') == self::UPYUN_MODEL){

            try{

                $content = $this->file->getPathname();
                $content = file_get_contents($content);

                //本地保存
                $this->file->move(dirname($this->filePath), $this->fileName);

                return $this->uploadUpyun('/'.dirname($this->filePath).'/'.$this->fileName, $content);

            } catch (FileException $exception) {
                $this->stateInfo = $this->getStateInfo("ERROR_WRITE_CONTENT");
                return false;
            }

        } else {
            $this->stateInfo = $this->getStateInfo("ERROR_UNKNOWN_MODE");
            return false;
        }

        return true;

    }
}
