<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Repository namespace 存储库命名空间
    |--------------------------------------------------------------------------
    | 存储库命的命名空间
    | The namespace for the repository classes.
    |
    */
    'repository_namespace' => 'App\Repositories',

    /*
    |--------------------------------------------------------------------------
    | Repository path 存储库路径
    |--------------------------------------------------------------------------
    | 存储库的路径
    | The path to the repository folder.
    |
    */
    'repository_path' => 'app' . DIRECTORY_SEPARATOR . 'Repositories',

    /*
    |--------------------------------------------------------------------------
    | Criteria namespace 标准命名空间
    |--------------------------------------------------------------------------
    | 标准的命名空间
    | The namespace for the criteria classes.
    |
    */
    'criteria_namespace' => 'App\Repositories\Criteria',

    /*
    |--------------------------------------------------------------------------
    | Criteria path 标准路径
    |--------------------------------------------------------------------------
    | 标准的路径
    | The path to the criteria folder.
    |
    */
    'criteria_path' => 'app' . DIRECTORY_SEPARATOR . 'Repositories' . DIRECTORY_SEPARATOR . 'Criteria',

    /*
    |--------------------------------------------------------------------------
    | Model namespace 模型命令空间
    |--------------------------------------------------------------------------
    | 模型的命名空间
    | The model namespace.
    |
    */
    'model_namespace' => 'App',
];