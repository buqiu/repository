# Laravel Repository

Laravel Repository 是 Laravel 6 的一个软件包，用于抽象数据库层。 这使应用程序易于维护。

## 安装

从终端运行以下命令:

```shell script
composer require buqiu/repository
```

## 使用

#### 首先,使用以下命令创建存储库类:

```shell script
php artisan make:repository Film
```
其中 `Film` 是现有模型的名称.如果模型不存在,它将为您生成.

#### 最后,在控制器中使用存储库:

```php
<?php

namespace App\Http\Controllers;

use App\Repository\UserRepository;

class FilmsController extends Controller {

    /**
     * @var UserRepository 
     */
    private $filmRepository;

    public function __construct(UserRepository $userRepository) 
    {
        $this->filmRepository = $userRepository;
    }
    
    /**
     * @return mixed
     */
    public function index() 
    {
        return response()->json($this->userRepository->all());
    }
}
```

#### 发布配置

如果您希望覆盖存储库和条件所在的路径,请发布配置文件:

```shell script
php artisan vendor:publish --provider="Buqiu\Repository\Providers\RepositoryProvider"
```

然后只需打开 `config/repositories.php` 并编辑即可！

## 可用方法

可以使用以下方法:
Buqiu\Repository\Contracts\RepositoryInterface

```shell script
public function all($columns = ['*'])
public function lists($value, $key = null)
public function paginate($perPage = 1, $columns = ['*'], $method = 'full');
public function create(array $data)
// 如果你使用的是 mongodb,则需要指定主键 $attribute
public function update(array $data, $id, $attribute = 'id')
public function destroy($id)
public function destroyWhere(array $attributes)
public function orderBy(string $columns, $direction = 'ASC')
public function with(array $relations)
public function withCount($relations)
public function whereHas(string $relation, \Closure $callback = null)
public function orWhereHas(string $relation, \Closure $callback = null)
public function find($id, $columns = ['*'])
public function findByField($field, $value, $columns = ['*'])
public function getByField($field, $value, $columns = ['*'])
public function findByAttributes(array $attributes = [], array $columns = array('*'))
public function getByAttributes(array $attributes = [], array $columns = array('*'))
public function paginateByAttributes(array $attributes = [], int $perPage = 20, array $columns = array('*'), $method = 'full')
```

Buqiu\Repository\Contracts\CriteriaInterface

```shell script
public function apply($model, Repository $repository)
```

## 用法示例

在存储库中创建新 `user`

```shell script
$this->userRepository->create(Input::all());
```

更新现用的 `user`

```shell script
$this->userRepository->update(Input::all(), $user_id);
```

删除 `user`

```shell script
$this->userRepository->destroy($id);
```

通过 `user_id` 查找 `user`

```shell script
$this->userRepository->find($id);
```

您还可以选择要提取的字段

```shell script
$this->userRepository->find($id, ['name', 'mobile', 'email']);
```

根据单个条件查询单条数据

```shell script
$this->userRepository->findByField('mobile', $mobile);
```

或者你可以通过单个条件获取所有数据

```shell script
$this->userRepository->getByField('user_id', $user_id);
```

根据多个字段获取所有结果

```shell script
$this->userRepository->getByAttributes([
  'sex' => $sex,
  ['year', '>', '$year']
]);
```

根据属性条件获取多条并且分页数据

```shell script
$this->userRepository->paginateByAttributes([
  'sex' => $sex,
  ['year', '>', '$year']
]);
```

## Criteria(标准)

标准是将特定标准或标准集应用于存储库查询的简单方法。

要创建一个Criteria类，请运行以下命令：

```shell script
php artisan make:criteria LengthOverTwoHours --model=user
```

以下是示例条件:

```php
<?php

namespace App\Repository\Criteria\Films;

use Buqiu\Repository\Criteria\Criteria;
use Buqiu\Repository\Contracts\RepositoryInterface;

class LengthOverTwoHours extends Criteria 
{
    /**
     * @param $model
     * @param RepositoryInterface $repository
     *                                       
     * @return Model
     */
    public function apply($model, RepositoryInterface $repository)
    {
        return $model->where('length', '>', 120);
    }
}
```

现在,在控制器类内部,您可以调用pushCriteria方法:

```php
<?php 

namespace App\Http\Controllers;

use App\Repositories\FilmRepository;
use App\Repositories\Criteria\Films\LengthOverTwoHours;

class FilmsController extends Controller 
{
    /**
     * @var FilmRepository
     */
    private $filmRepository;

    public function __construct(FilmRepository $filmRepository) 
    {
        $this->filmRepository = $filmRepository;
    }

    public function index() 
    {
        $this->filmRepository->pushCriteria(new LengthOverTwoHours());
        
        return response()->json($this->filmRepository->all());
    }
}
```

## 测试

```shell script
$ vendor/bin/phpunit
```

## license

Pouch is released under [the MIT License](LICENSE).
