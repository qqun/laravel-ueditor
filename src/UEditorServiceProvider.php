<?php

namespace QQun\UEditor;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Routing\Router;

class UEditorServiceProvider extends RouteServiceProvider
{

    protected $defer = false;


    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(Router $route)
    {
        parent::boot($route);
        $view = realpath(__DIR__ . '/../resources/views');
        $this->loadViewsFrom($view, 'UEditor');
        $this->publishes([
            realpath(__DIR__ . '/../resources/views') => base_path('resources/views/vendor/UEditor'),
        ], 'view');


        $this->publishes([
            realpath(__DIR__ . '/../resources/public') => public_path() . '/ueditor',
        ], 'assets');


        $locale = str_replace('_', '-', strtolower(config('app.locale')));

//        $this->loadTranslationsFrom(realpath(__DIR__.'/../resources/lang/'.$locale),'UEditor');
        $this->loadTranslationsFrom(realpath(__DIR__ . '/../resources/lang'), 'UEditor');


//        $this->publishes([
//            realpath(__DIR__.'/../resources/lang') => resource_path('lang')
//        ]);


        $file = "/ueditor/lang/$locale/$locale.js";
        $filePath = public_path() . $file;

        if (!\File::exists($filePath)) {
            $file = "/editor/lang/zh-CN/zh-CN.js";
        }


        \View::share('UeditorLangFile', $file);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        parent::register();
        $configPath = realpath(__DIR__ . '/../config/Ueditor.php');
        $this->mergeConfigFrom($configPath, 'Ueditor');
        $this->publishes([$configPath => config_path('Ueditor.php')], 'config');

    }

    public function map()
    {
        $router = app('router');

        $config = config('Ueditor.core.route', []);
        $config['namespace'] = __NAMESPACE__;

        //定义路由
        $router->group($config, function ($router) {
            $router->any('/ueditor/server', 'Controller@server');
        });
    }


}