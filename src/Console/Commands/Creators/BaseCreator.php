<?php

namespace Buqiu\Repository\Console\Commands\Creators;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Buqiu\Repositories\Console\Commands\BaseCommand;

abstract class BaseCreator
{
    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $model;

    /**
     * @var BaseCommand
     */
    protected $command;

    /**
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param string $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * 创建存储库
     * Create the repository.
     *
     * @param string $name
     * @param string $model
     * @param BaseCommand $command
     *
     * @return int
     */
    public function create($name, $model, BaseCommand $command)
    {
        // Set the criteria.
        $this->command = $command;

        // Set the name.
        $this->setName($name);

        // Set the model.
        $this->setModel($model);

        // Create the folder directory.
        $this->createDirectory();

        // Return result.
        return $this->createClass();
    }

    /**
     * 获取存储库目录
     * Get the repository directory.
     *
     * @return mixed
     */
    protected function getDirectory()
    {
        // Return the directory Get the criteria path from the config file.
        return Config::get("repositories.{$this->command->getCurrentEntity()}_path");
    }

    /**
     * 创建必要的目录
     * Create the necessary directory.
     */
    protected function createDirectory()
    {
        // Directory
        $directory = $this->getDirectory();

        // Check if the directory exists.
        if (!$this->files->isDirectory($directory)) {
            // Create the directory if not.
            $this->files->makeDirectory($directory, 0755, true);
        }
    }

    /**
     * 获取路径
     * Get the path.
     *
     * @return string
     */
    protected function getPath()
    {
        // Return the path.
        return $this->getDirectory().DIRECTORY_SEPARATOR.$this->getName().'.php';
    }

    /**
     * 获取模板
     * Get the stub.
     *
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function getStub()
    {
        // Return the stub.
        return $this->files->get($this->getStubPath().$this->command->getCurrentEntity().'.stub');
    }

    /**
     * 获取模板路径
     * Get the stub path.
     *
     * @return string
     */
    protected function getStubPath()
    {
        // Return the path.
        return __DIR__.'/../../../../resources/stubs/';
    }

    /**
     * 填充模板
     * Populate the stub.
     *
     * @return mixed string|string[]
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function populateStub()
    {
        // Populate data
        $populate_data = $this->getPopulateData();
        // Stub
        $stub = $this->getStub();

        // Loop through the populate data.
        foreach ($populate_data as $key => $value) {
            // Populate the stub.
            $stub = str_replace($key, $value, $stub);
        }

        // Return the stub.
        return $stub;
    }

    /**
     * 生成类文件
     * Generate the class file.
     *
     * @return bool
     */
    abstract protected function createClass();

    /**
     * 获取模板的替换数据
     * Fetch the replacement data for the stub.
     *
     * @return array
     */
    abstract protected function getPopulateData();
}
