<?php

namespace QQun\UEditor;

use Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use QQun\UEditor\Uploader\Upload;
use QQun\UEditor\Uploader\UploadCatch;
use QQun\UEditor\Uploader\UploadFile;
use QQun\UEditor\Uploader\UploadScrawl;



// use Symfony\Component\HttpFoundation\File\UploadedFile;

class Controller extends BaseController
{

    public function __construct()
    {
    }

    public function server(Request $request)
    {
        $config = config::get('Ueditor.upload');
        $action = $request->get('action');

        $result = [];

        switch ($action) {

            case 'config':
                $result = $config;
                break;
            case 'uploadimage':
                $upConfig = [
                    'pathFormat' => $config['imagePathFormat'],
                    'maxSize' => $config['imageMaxSize'],
                    'allowFiles' => $config['imageAllowFiles'],
                    'fieldName' => $config['imageFieldName'],
                ];

                $result = with(new UploadFile($upConfig, $request))->upload();
                break;
            case 'uploadscrawl':
                $upConfig = [
                    'pathFormat' => $config['scrawlPathFormat'],
                    'maxSize' => $config['scrawlMaxSize'],
                    'oriName' => $config['scrawl.png'],
                    'fieldName' => $config['scrawlFieldName'],
                ];
                $result = with(new UploadScrawl($upConfig, $request))->upload();
                break;
            case 'uploadvideo':
                $upConfig = [
                    'pathFormat' => $config['videoPathFormat'],
                    'maxSize' => $config['videoMaxSize'],
                    'allowFiles' => $config['videoAllowFiles'],
                    'fieldName' => $config['videoFieldName'],
                ];
                $result = with(new UploadFile($upConfig, $request))->upload();
                break;
            case 'uploadfile':
            default:
                $upConfig = [
                    'pathFormat' => $config['filePathFormat'],
                    'maxSize' => $config['fileMaxSize'],
                    'allowFiles' => $config['fileAllowFiles'],
                    'fieldName' => $config['fileFieldName'],
                ];

                $result = with(new UploadFile($upConfig, $request))->upload();
                break;

            /* 图片列表 */
            case 'listimage':

                if (config('Ueditor.core.mode') == Upload::LOCAL_MODEL) {
                    $result = with(new Lists(
                        $config['imageManagerAllowFiles'],
                        $config['imageManagerListSize'],
                        $config['imageManagerListPath'],
                        $request
                    ))->getList();
                } else if (config('Ueditor.core.mode') == Upload::QINIU_MODEL) {
                    $result = with(new ListsQiniu(
                        $config['imageManagerAllowFiles'],
                        $config['imageManagerListSize'],
                        $config['imageManagerListPath'],
                        $request
                    ))->getList();
                } else if(config('Ueditor.core.mode') == Upload::UPYUN_MODEL){
                    $result = with(new ListsUpyun(
                        $config['imageManagerAllowFiles'],
                        $config['imageManagerListSize'],
                        $config['imageManagerListPath'],
                        $request
                    ))->getList();
                }else if(config('Ueditor.core.mode') == Upload::SCS_MODEL){
                    $result = with(new ListsSCS(
                        $config['imageManagerAllowFiles'],
                        $config['imageManagerListSize'],
                        $config['imageManagerListPath'],
                        $request
                    ))->getList();
                }
                break;
            /* 列出文件 */
            case 'listfile':
                if (config('Ueditor.core.mode') == Upload::LOCAL_MODEL) {
                    $result = with(new Lists(
                        $config['fileManagerAllowFiles'],
                        $config['fileManagerListSize'],
                        $config['fileManagerListPath'],
                        $request))->getList();
                } else if (config('Ueditor.core.mode') == Upload::QINIU_MODEL) {
                    $result = with(new ListsQiniu(
                        $config['fileManagerAllowFiles'],
                        $config['fileManagerListSize'],
                        $config['fileManagerListPath'],
                        $request))->getList();
                }else if(config('Ueditor.core.mode') == Upload::UPYUN_MODEL){
                    $result = with(new ListsUpyun(
                        $config['fileManagerAllowFiles'],
                        $config['fileManagerListSize'],
                        $config['fileManagerListPath'],
                        $request
                    ))->getList();
                }else if(config('Ueditor.core.mode') == Upload::SCS_MODEL){
                    $result = with(new ListsSCS(
                        $config['fileManagerAllowFiles'],
                        $config['fileManagerListSize'],
                        $config['fileManagerListPath'],
                        $request
                    ))->getList();
                }

                break;

            /* 抓取远程文件 */
            case 'catchimage':

                $upConfig = array(
                    "pathFormat" => $config['catcherPathFormat'],
                    "maxSize" => $config['catcherMaxSize'],
                    "allowFiles" => $config['catcherAllowFiles'],
                    "oriName" => "remote.png",
                    'fieldName' => $config['catcherFieldName'],
                );

                $sources = Input::get($upConfig['fieldName']);
                $list = [];
                if(is_array($sources)) {
                    foreach ($sources as $imgUrl) {
                        $upConfig['imgUrl'] = $imgUrl;
                        $info = with(new UploadCatch($upConfig, $request))->upload();

                        array_push($list, array(
                            "state" => $info["state"],
                            "url" => $info["url"],
                            "size" => $info["size"],
                            "title" => htmlspecialchars($info["title"]),
                            "original" => htmlspecialchars($info["original"]),
                            "source" => htmlspecialchars($imgUrl)
                        ));
                    }
                }
                $result = [
                    'state' => count($list) ? 'SUCCESS' : 'ERROR',
                    'list' => $list
                ];


                break;
            // default:
            // $result = $config;
            // break;
        }

//        dd($result);
        return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
    }


}
