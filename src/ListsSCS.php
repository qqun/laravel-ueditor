<?php
namespace QQun\UEditor;

use SCS;

/**
 * 文件列表 for SinaCloudStorage
 * Class ListsSCS
 * @package QQun\UEditor
 */
class ListsSCS
{

    public function __construct($allowFiles, $listSize, $path, $request = null)
    {
        $this->allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);
        $this->listSize = $listSize;
        $this->path = $path;
        $this->request = $request;
    }


    public function getList()
    {

        $size = $this->request->get('size', $this->listSize);
        $start = $this->request->get('start', '');
        $end = $start + $size;

        $scs_conf = config('Ueditor.core.scs');
        $scs = new SCS($scs_conf['accessKey'], $scs_conf['secretKey']);
        $file_list = $scs->getBucket($scs_conf['bucket']);

        foreach ($file_list as $value) {
            if ($_GET['action'] == 'listimage') {
                preg_match("/^.*(uploads)\/(image).*(" . $this->allowFiles . ")/", $value['name'], $urls);
            } else if ($_GET['action'] == 'listfile') {
                preg_match("/^.*(uploads)\/(file).*(" . $this->allowFiles . ")/", $value['name'], $urls);
            }
            if ($urls) {
                $files[] = array('url' => $scs_conf['url'] . '/' . $scs_conf['bucket'] . "/" . $value['name'], 'mtime' => $value['time']);
            }
        }

        if (!count($files)) {
            return json_encode(array(
                "state" => "no match file",
                "list" => array(),
                "start" => $start,
                "total" => count($files)
            ));
        }
        /* 获取指定范围的列表 */
        $len = count($files);
        for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--) {
            $list[] = $files[$i];
        }


        return [
            "state" => "SUCCESS",
            "list" => $list,
            "start" => $start,
            "total" => count($files)
        ];
    }
}