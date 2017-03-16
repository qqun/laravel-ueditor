<?php
namespace QQun\UEditor;

/**
 * 列表文件 for UpYun
 * Class ListsUpyun
 * @package QQun\UEditor
 */
class ListsUpyun
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

        $up = config('Ueditor.core.upyun');
        $upYun = new \UpYun($up['bucket'], $up['username'], $up['password'], $up['endpoint'], $up['timeout']);

        try {
            //组合目录
            //TODO::只获取最新一组文件
            $file_list = $upYun->getList($this->path);
            $file_list = self::getFiles($upYun, $this->path . $file_list[0]['name'], $this->allowFiles);

            /* 返回数据 */
            $result = [
                "state" => "SUCCESS",
                "list" => $file_list,
                "start" => $start,
                "total" => count($file_list)
            ];

            return $result;
        } catch (\Exception $e) {
            $e->getMessage();
        }


        return [
            "state" => "no match file",
            "list" => array(),
            "start" => $start,
            "total" => 0
        ];


    }


    /**
     * 遍历获取目录下的指定类型的文件
     * @param $object
     * @param $path
     * @param $allowFiles
     * @return array
     */
    protected function getFiles($object, $path, $allowFiles)
    {
        // 最后上传在前面
        $files = [];
        $handle = $object->getList($path);
        $count = count($handle) - 1;
        for ($i = $count; $i >= 0; $i--) {
            if ($handle[$i]['type'] == 'file') {
                if (preg_match("/.(" . $allowFiles . ")/", $handle[$i]['name'])) {
                    $files[] = [
                        'url' => config('Ueditor.core.upyun.url') . $path . '/' . $handle[$i]['name'],
                        'mtime' => $handle[$i]['time']
                    ];
                }
            }
        }
        return $files;
    }

}