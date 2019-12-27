<?php

namespace Buqiu\Repository\Console\Commands\Creators;

use Illuminate\Support\Facades\Config;
use Doctrine\Common\Inflector\Inflector;

class CriteriaCreator extends BaseCreator
{

    /**
     * 创建存储库类
     * Create the repository class.
     *
     * @return bool|int
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function createClass()
    {
        // Result.
        if ($this->files->exists($this->getPath())) {
            throw new \RuntimeException("The criteria with the name '{$this->getName()}' already exists.");
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
        // Criteria.
        $criteria = $this->getName();
        // Model
        $model = $this->pluralizeModel();

        // Criteria namespace.
        $criteria_namespace = Config::get('repositories.criteria_namespace');

        // Criteria class.
        $criteria_class = $criteria;

        // Check if the model isset and not empty.
        if (isset($model) && !empty($model)) {
            // Update the criteria namespace with the model folder.
            $criteria_namespace .= '\\'.$model;
        }

        // Populate data.
        $populate_data = [
            'criteria_namespace' => $criteria_namespace,
            'criteria_class' => $criteria_class,
        ];

        // Return the populate data.
        return $populate_data;
    }

    /**
     * 使模型多元化
     * Pluralize the model.
     *
     * @return string
     */
    protected function pluralizeModel()
    {
        // Pluralized
        $pluralized = Inflector::pluralize($this->getModel());

        // Uppercase first character the modelname
        $model_name = ucfirst($pluralized);

        // Return the pluralized model.
        return $model_name;
    }
}
