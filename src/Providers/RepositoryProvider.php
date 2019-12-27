<?php

namespace Buqiu\Repository\Providers;

use Illuminate\Support\Composer;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Buqiu\Repository\Console\Commands\CriteriaMakeCommand;
use Buqiu\Repository\Console\Commands\RepositoryMakeCommand;
use Buqiu\Repository\Console\Commands\Creators\CriteriaCreator;
use Buqiu\Repository\Console\Commands\Creators\RepositoryCreator;

class RepositoryProvider extends ServiceProvider
{
    /**
     * 指示是否推迟提供程序的加载
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * 引导应用程序服务
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Config path.
        $config_path = __DIR__.'/../../config/repositories.php';

        // Publish config.
        $this->publishes(
            [$config_path => config_path('repositories.php')],
            'repositories'
        );
    }

    /**
     * 注册应用程序服务
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Register bindings.
        $this->registerBindings();

        // Register make repository command.
        $this->registerMakeRepositoryCommand();

        // Register make criteria command.
        $this->registerMakeCriteriaCommand();

        // Register commands
        $this->commands(['command.repository.make', 'command.criteria.make']);

        // Config path
        $config_path = __DIR__.'/../../config/repositories.php';

        // Merge config.
        $this->mergeConfigFrom(
            $config_path,
            'repositories'
        );
    }

    /**
     * 注册绑定
     * Register the bindings.
     */
    protected function registerBindings()
    {
        // FileSystem.
        $this->app->instance('FileSystem', new Filesystem());

        // Composer.
        $this->app->bind(
            'Composer',
            function ($app) {
                return new Composer($app['FileSystem']);
            }
        );

        // Repository creator.
        $this->app->singleton(
            'RepositoryCreator',
            function ($app) {
                return new RepositoryCreator($app['FileSystem']);
            }
        );

        // Criteria creator.
        $this->app->singleton(
            'CriteriaCreator',
            function ($app) {
                return new CriteriaCreator($app['FileSystem']);
            }
        );
    }

    /**
     * 注册 make:repository 命令
     * Register the make:repository command.
     */
    protected function registerMakeRepositoryCommand()
    {
        // Make repository command.
        $this->app->singleton('command.repository.make', function ($app) {
            return new RepositoryMakeCommand($app['RepositoryCreator']);
        });

    }

    /**
     * 注册 the make:criteria 命令
     * Register the make:criteria command.
     */
    protected function registerMakeCriteriaCommand()
    {
        // make criteria command.
        $this->app->singleton('command.criteria.make', function ($app) {
            return new CriteriaMakeCommand($app['CriteriaCreator']);
        });

    }

    /**
     * 获取提供商提供的服务
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'command.repository,make',
            'command.criteria.make',
        ];
    }
}
