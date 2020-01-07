<?php

namespace Buqiu\Repository\Console\Commands\Creators;

use Doctrine\Common\Inflector\Inflector;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class RepositoryCreator extends BaseCreator
{

    /**
     * 创建类
     * Creates the class.
     *
     * @return bool|int
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function createClass()
    {
        $model = trim(str_replace('/', '\\', Config::get('repositories.model_namespace').'\\'.$this->name), '\\');

        if (!class_exists($model)) {
            if ($this->command->confirm("Do you want to create a {$model} model?")) {
                $modelName = str_replace('App\\', '', $model);
                Artisan::call('make:model', ['name' => $modelName ?? $this->name]);
            } else {
                throw new \RuntimeException("Could not create repository: Model {$model} does not exist.");
            }
        }

        // Result.
        if ($this->files->exists($this->getPath())) {

            throw new \RuntimeException("The repository with the name '{$this->getName()}' already exists.");
        }

        // Return the result.
        return $this->files->put($this->getPath(), $this->populateStub());
    }

    /**
     * 获取填充数据
     * Get the populate data.
     *
     * @return array
     */
    protected function getPopulateData()
    {
        // Repository namespace.
        $repositoryNamespace = Config::get('repositories.repository_namespace');

        // Repository class.
        $repositoryClass = $this->getName();
        if (Str::is('*/*', $repositoryClass)) {
            $directory = $this->getDirectory().'/'.Str::before($repositoryClass, '/');
            // 判断目录是否存在
            if (!$this->files->isDirectory($directory)) {
                $this->files->makeDirectory($directory);
            }
            // 重定义命名空间
            $repositoryNamespace = $repositoryNamespace.'\\'.Str::before($repositoryClass, '/');
            // 重定义存储库类名
            $repositoryClass = Str::after($repositoryClass, '/');
        }

        // Model path.
        $modelPath = Config::get('repositories.model_namespace');

        // Model use name
        $modelName = $this->getModelName();
        $modelUseName = trim(str_replace('/', '\\', $modelName), '\\');

        // Model name.
        if (Str::is('*/*', $modelName)) {
            $modelName = Str::after($modelName, '/');
        }

        // Populate data.
        $populateData = [
            'repository_namespace' => $repositoryNamespace,
            'repository_class' => $repositoryClass,
            'model_use_name' => $modelUseName,
            'model_path' => $modelPath,
            'model_name' => $modelName,
        ];

        // Return populate_data
        return $populateData;
    }

    /**
     * 获取存储库名称
     * Get the repository name.
     *
     * @return mixed|string
     */
    public function getName()
    {
        // Get the repository.
        $repositoryName = parent::getName();

        // Check if the repository ends with 'Repository'.
        if (!strpos($repositoryName, 'Repository') !== false) {
            // Append 'Repository' if not.
            $repositoryName .= 'Repository';
        }

        // Return repository name.
        return $repositoryName;
    }

    /**
     * 获取模型名称
     * Get the model name.
     *
     * @return string
     */
    public function getModelName()
    {
        // Set model.
        $model = $this->getModel();

        // Return the model name,
        // Check if the model isset,
        // Set the model name from the model option,
        // Set the model name by the stripped repository name.
        return isset($model) && !empty($model) ? $model : Inflector::singularize($this->stripRepositoryName());
    }

    /**
     * 获取剥离的存储库名称
     * Get the stripped repository name.
     *
     * @return string
     */
    private function stripRepositoryName()
    {
        // Remove repository from the string.
        $stripped = str_ireplace('repository', '', $this->getName());

        // Uppercase repository name.
        $result = ucfirst($stripped);

        // Return the result.
        return $result;
    }
}
