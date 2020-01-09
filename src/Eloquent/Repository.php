<?php

namespace Buqiu\Repository\Eloquent;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Container\Container as App;
use Illuminate\Contracts\Pagination\Paginator;
use Buqiu\Repository\Criteria\Criteria;
use Buqiu\Repository\Contracts\CriteriaInterface;
use Buqiu\Repository\Contracts\RepositoryInterface;
use Buqiu\Repository\Exceptions\RepositoryException;

abstract class Repository implements RepositoryInterface, CriteriaInterface
{
    /**
     * @var App
     */
    protected $app;

    /**
     * @var
     */
    protected $model;

    /**
     * @var
     */
    protected $newModel;

    /**
     * @var Collection
     */
    protected $criteria;

    /**
     * @var bool
     */
    protected $skipCriteria = false;

    /**
     * 防止覆盖链使用中的相同条件
     * Prevents from overwriting same criteria in chain usage
     * @var bool
     */
    protected $preventCriteriaOverwriting = true;

    /**
     * 初始化
     * Repository constructor.
     * @param App $app
     * @param Collection $collection
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __construct(App $app, Collection $collection)
    {
        $this->app = $app;
        $this->criteria = $collection;
        $this->resetScope();
        $this->makeModel();
    }

    /**
     * 指定模型类名称
     * Specify Model class name
     *
     * @return mixed
     */
    public abstract function model();

    /**
     * @return Model
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function makeModel()
    {
        return $this->setModel($this->model());
    }

    /**
     * 设置 Eloquent 模型实例化
     * Set Eloquent Model to instantiate
     *
     * @param $eloquentModel
     * @return Model
     * @throws RepositoryException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function setModel($eloquentModel)
    {
        $this->newModel = $this->app->make($eloquentModel);
        if (!$this->newModel instanceof Model) {
            throw new RepositoryException("Class {$this->newModel} must be an instance of " . Model::class);
        }

        return $this->model = $this->newModel;
    }

    /**
     * 返回模型的干净实体
     * Returns clean entity of model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModel()
    {
        return $this->newModel;
    }

    /**
     * @return $this
     */
    public function resetScope()
    {
        $this->skipCriteria(false);

        return $this;
    }

    /**
     * @param bool $status
     *
     * @return $this|mixed
     */
    public function skipCriteria($status = true)
    {
        $this->skipCriteria = $status;

        return $this;
    }

    /**
     * @return Collection|mixed
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * @param Criteria $criteria
     *
     * @return $this|mixed
     */
    public function getByCriteria(Criteria $criteria)
    {
        $this->model = $criteria->apply($this->model, $this);

        return $this;
    }

    /**
     * @param Criteria $criteria
     *
     * @return $this|mixed
     */
    public function pushCriteria(Criteria $criteria)
    {
        if ($this->preventCriteriaOverwriting) {
            // Find existing criteria
            $key = $this->criteria->search(function ($item) use ($criteria) {
                return (is_object($item) && (get_class($item) == get_class($criteria)));
            });

            // Remove old criteria
            if (is_int($key)) {
                $this->criteria->offsetUnset($key);
            }
        }

        $this->criteria->push($criteria);

        return $this;
    }

    /**
     * @return $this|mixed
     */
    public function applyCriteria()
    {
        if ($this->skipCriteria === true) {
            return $this;
        }

        foreach ($this->getCriteria() as $criteria) {
            if ($criteria instanceof Criteria) {
                $this->model = $criteria->apply($this->model, $this);
            }
        }

        return $this;
    }

    /**
     * 获取所有数据
     *
     * @param array $columns 字段 * 代表所有字段
     *
     * @return mixed|void
     */
    public function all(array $columns = array('*'))
    {
        $this->applyCriteria();

        return $this->model->get($columns);
    }

    public function lists($value, $key = null)
    {
        $this->applyCriteria();

        $lists = $this->model->pluck($value, $key);

        if (is_array($lists)) {
            return $lists;
        }

        return $lists->all();
    }

    /**
     * 获取分页数据
     *
     * @param int $perPage 每页条数
     * @param array $columns 字段 * 代表所有字段
     * @param string $method 分页格式
     *
     * @return Paginator
     */
    public function paginate(int $perPage = 25, array $columns = array('*'), $method = 'full')
    {
        $this->applyCriteria();

        $paginationMethod = $method !== 'full' ? 'simplePaginate' : 'paginate';

        return $this->model->$paginationMethod($perPage, $columns);
    }

    /**
     * 创建数据
     *
     * @param array $data 数据
     *
     * @return mixed|void
     */
    public function create(array $data = [])
    {
        return $this->model->create($data);
    }

    /**
     * 保存模型而无需进行大量分配
     * save a model without massive assignment.
     *
     * @param array $data
     *
     * @return mixed
     */
    public function saveModel(array $data)
    {
        foreach ($data as $key => $value) {
            $this->model->$key = $value;
        }

        return $this->model->save();
    }

    /**
     * 修改数据
     *
     * @param array $data 数据
     * @param mixed $id 属性值
     * @param string $attribute 属性条件
     *
     * @return mixed|void
     */
    public function update(array $data, $id, $attribute = 'id')
    {
        return $this->where($attribute, '=', $id)->update($data);
    }

    /**
     * 删除数据
     *
     * @param mixed $id 属性值
     *
     * @return mixed
     */
    public function destroy($id)
    {
        return $this->model->destroy($id);
    }

    /**
     * 按给定条件删除多条数据
     *
     * @param array $attributes 属性条件
     *
     * @return mixed
     */
    public function destroyWhere(array $attributes)
    {
        $this->applyCriteria();

        $this->buildQueryByAttributes($attributes);

        return $this->model->delete();
    }

    /**
     * @param string $columns
     * @param string $direction
     *
     * @return mixed|void
     */
    public function orderBy(string $columns, $direction = 'ASC')
    {
        $this->model = $this->model->orderBy($columns, $direction);

        return $this;
    }

    /**
     * 设置希望加载的关系
     *
     * @param array $relations 关系
     *
     * @return mixed
     */
    public function with(array $relations)
    {
        $this->model = $this->model->with($relations);

        return $this;
    }

    /**
     * 添加子选择查询以统计关系
     *
     * @param mixed $relations 关系
     *
     * @return mixed
     */
    public function withCount($relations)
    {
        $this->model = $this->model->withCount($relations);

        return $this;
    }

    /**
     * 使用where子句向查询添加并且关系条件
     *
     * @param string $relation 关系
     * @param \Closure|null $callback 返回
     *
     * @return mixed
     */
    public function whereHas(string $relation, \Closure $callback = null)
    {
        $this->model = $this->model->whereHas($relation, $callback);

        return $this;
    }

    /**
     * 使用where子句向查询添加或关系条件
     *
     * @param string $relation 关系
     * @param \Closure|null $callback 返回
     *
     * @return mixed
     */
    public function orWhereHas(string $relation, \Closure $callback = null)
    {
        $this->model = $this->model->orWhereHas($relation, $callback);

        return $this;
    }

    /**
     * 根据主键查找单条数据
     *
     * @param int $id ID
     * @param array $columns 字段 * 代表所有字段
     *
     * @return mixed
     */
    public function find(int $id, array $columns = array('*'))
    {
        $this->applyCriteria();

        return $this->model->find($id, $columns);
    }

    /**
     * 根据多主键查询所有数据
     *
     * @param array $ids //IDS
     * @param array $columns 字段 * 代表所有字段
     *
     * @return mixed
     */
    public function findByMany(array $ids = [], $columns = array('*'))
    {
        $this->applyCriteria();

        return $this->model->whereIn('id', $ids)->get($columns);
    }

    /**
     * 根据条件查询单条数据
     * Find data by field and value
     *
     * @param string $attribute 字段
     * @param string $value 值
     * @param array $columns 字段 * 代表所有字段
     *
     * @return mixed
     */
    public function findByField(string $attribute, string $value, array $columns = array('*'))
    {
        $this->applyCriteria();

        return $this->model->where($attribute, '=', $value)->first($columns);
    }

    /**
     * 根据多属性条件单条查询
     *
     * @param array $attributes 多属性条件
     * @param array $columns 字段 * 代表所有字段
     *
     * @return mixed
     */
    public function findByAttributes(array $attributes = [], array $columns = array('*'))
    {
        $this->applyCriteria();
        $this->buildQueryByAttributes($attributes);

        return $this->model->first($columns);
    }

    /**
     * 根据条件查询所有数据
     *
     * @param string $attribute 字段
     * @param string $value 值
     * @param array $columns 字段 * 代表所有字段
     *
     * @return mixed|void
     */
    public function getByField(string $attribute, string $value, array $columns = array('*'))
    {
        $this->applyCriteria();

        return $this->model->where($attribute, '=', $value)->get($columns);
    }

    /**
     * 根据多属性条件获取多条
     *
     * @param array $attributes 查询属性条件
     * @param array $columns 字段 * 代表所有字段
     *
     * @return mixed
     */
    public function getByAttributes(array $attributes = [], array $columns = array('*'))
    {
        $this->applyCriteria();
        $this->buildQueryByAttributes($attributes);

        return $this->model->get($columns);
    }

    /**
     * 根据属性条件获取多条并且分页数据
     *
     * @param array $attributes 查询属性条件
     * @param int $perPage 分页数据
     * @param array $columns 字段 * 代表所有字段
     * @param string $method 分页格式
     *
     * @return mixed
     */
    public function paginateByAttributes(array $attributes = [], int $perPage = 20, array $columns = array('*'), $method = 'full')
    {
        $this->applyCriteria();
        $this->buildQueryByAttributes($attributes);

        return $this->model->paginate($perPage, $columns, $method);
    }

    /**
     * 组合查询条件
     *
     * @param array $attributes 查询属性条件
     *
     * @return mixed
     */
    protected function buildQueryByAttributes(array $attributes)
    {
        foreach ($attributes as $field => $value) {
            if (is_array($value)) {
                list($field, $condition, $val) = $value;
                $this->model = $this->model->where($field, $condition, $val);
            } else {
                $this->model = $this->model->where($field, '=', $value);
            }
        }
    }
}
