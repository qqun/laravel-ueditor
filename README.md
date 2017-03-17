# UEditor for Laravel5

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]


UEditor是由百度web前端研发部开发所见即所得富文本web编辑器，具有轻量，可定制，注重用户体验等特点，开源基于MIT协议，允许自由使用和修改代码...

本项目采用UEditor的php版1.4.3.3版

文件上传支持：

* 本地存储
* 七牛云存储
* 又拍云

默认为本地存储，存储路径为 public/uploads

部分内容参考了 [stevenyangecho/laravel-u-editor](https://github.com/stevenyangecho/laravel-u-editor) 感谢.

更多的是为了自用方便.

## 安装

需要PHP版本 5.6+，项目已经安装Composer

#### 获取扩展

运行：

``` bash
$ composer require qqun/laravel-ueditor
```

或者修改composer.json 增加：

```
"qqun/laravel-ueditor": "*"
```


运行

``` bash
composer install
```

或者

```bash
composer update
```
更新自动加载项

#### 更新配置

修改config/app.php，在providers里增加：

``` php
QQun\UEditor\UEditorServiceProvider::class,
```

然后运行

``` bash
php artisan vendor:publish
```



## 配置

若以上操作成功， 则会自动创建配置文件 config/Ueditor.php

``` php
    /*
    |------------------------------------
    | 新增配置,route
    |------------------------------------
    |
    |注意权限验证,请自行添加middleware
    |middleware 相当重要,请根据自己的项目设置,
    |比如如果在后台使用,请设置为后台的auth middleware.
    |如果是单纯本机测试,请将
    |`// 'middleware' => 'auth',` 直接注释掉,
    |如果留 `'middleware'=>''`空值,会产生bug,原因不详.
    |
    |
    */
    'core' => [
        'route' => [
            // 'middleware' => 'auth',
        ],
        'mode'=>'local',//上传方式,local 为本地   qiniu 为七牛
        'baseurl' => '',

        //七牛配置,若mode='qiniu',以下为必填.
        'qiniu'=>[
            'accessKey'=>'',
            'secretKey'=>'',
            'bucket'=>'',
            'url'=>'http://xxx.clouddn.com',
            //七牛的CDN域名,注意带上http://

        ],

        'upyun' => [
            'bucket' => '',     //UpYun 空间名称
            'username' => '',
            'password' => '',
            /*
             * 网络接入点
             * Upyun::ED_AUTO 根据网络条件自动选择接入点
             * Upyun::ED_TELECOM 电信接入点
             * Upyun::ED_CNC 联通网通接入点
             * Upyun::ED_CTT 移动铁通接入点
             */
            'endpoint' => null,   // 默认自动选择
            'timeout' => 60,     // 默认 30
            'url' => 'http://xxx.b0.upaiyun.com'    // UpYun的CDN域名 注意带上http://
        ]
    ],
```

UEditor 公共资源在 public/ueditor 内，可以根据需要自行修改



## 使用

模版内引入head

``` php
@include('UEditor::head')
```

引入成功， 会自动加载UEditor 相关js等文件

示例：

```
@include('UEditor::head')

<form method="post">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <!-- 加载编辑器的容器 -->
    <textarea id="container" name="content">这里写你的初始化内容</textarea>
    <input type="submit" value="提交">
</form>

<script type="text/javascript">
    var ue = UE.getEditor('container', {
            initialFrameWidth : 500,
            initialFrameHeight : 450,
    });
    ue.ready(function() {
            //此处为支持laravel5 csrf ,根据实际情况修改,目的就是设置 _token 值.
            ue.execCommand('serverparam', '_token', '{{ csrf_token() }}');
    });
</script>
```
测试

```
Route::get("ueditor", function(){
	return View('vendor.UEditor.test');
});
```

更多使用方法参见：[http://ueditor.baidu.com](http://ueditor.baidu.com)




## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/qqun/laravel-ueditor.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/qqun/laravel-ueditor/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/qqun/laravel-ueditor.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/qqun/laravel-ueditor.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/qqun/laravel-ueditor.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/qqun/laravel-ueditor
[link-travis]: https://travis-ci.org/qqun/laravel-ueditor
[link-scrutinizer]: https://scrutinizer-ci.com/g/qqun/laravel-ueditor/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/qqun/laravel-ueditor
[link-downloads]: https://packagist.org/packages/qqun/laravel-ueditor
[link-author]: https://github.com/qqun
[link-contributors]: ../../contributors
