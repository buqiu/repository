<?php

namespace Buqiu\Repository\Contracts;

interface RepositoryInterface
{
    /**
     * 获取所有数据
     *
     * @param array $columns 字段 * 代表所有字段
     *
     * @return mixed
     */
    public function all(array $columns = array('*'));

    /**
     * 获取分页数据
     *
     * @param int $perPage 每页条数
     * @param array $columns 字段 * 代表所有字段
     * @param string $method 分页格式
     *
     * @return mixed
     */
    public function paginate(int $perPage = 20, array $columns = array('*'), $method = 'full');

    /**
     * 创建数据
     *
     * @param array $data 数据
     *
     * @return mixed
     */
    public function create(array $data = []);

    /**
     * 修改数据
     *
     * @param mixed $id 属性值
     * @param array $data 数据
     *
     * @return mixed
     */
    public function update(array $data, $id);

    /**
     * 删除数据
     *
     * @param mixed $id 属性值
     *
     * @return mixed
     */
    public function destroy($id);

    /**
     * 按给定条件删除多条数据
     *
     * @param array $attributes 属性条件
     *
     * @return mixed
     */
    public function destroyWhere(array $attributes);

    /**
     * 给指定字段排序
     *
     * @param string $columns 排序字段
     * @param string $direction 排序方向
     *
     * @return mixed
     */
    public function orderBy(string $columns, $direction = 'ASC');

    /**
     * 设置希望加载的关系
     *
     * @param array $relations 关系
     *
     * @return mixed
     */
    public function with(array $relations);

    /**
     * 添加子选择查询以统计关系
     *
     * @param mixed $relations 关系
     *
     * @return mixed
     */
    public function withCount($relations);

    /**
     * 使用where子句向查询添加并且关系条件
     *
     * @param string $relation 关系
     * @param \Closure|null $callback 返回
     *
     * @return mixed
     */
    public function whereHas(string $relation, \Closure $callback = null);

    /**
     * 使用where子句向查询添加或关系条件
     *
     * @param string $relation 关系
     * @param \Closure|null $callback 返回
     *
     * @return mixed
     */
    public function orWhereHas(string $relation, \Closure $callback = null);

    /**
     * 根据主键查找单条数据
     *
     * @param int $id ID
     * @param array $columns 字段 * 代表所有字段
     *
     * @return mixed
     */
    public function find(int $id, array $columns = array('*'));

    /**
     * 根据多主键查询所有数据
     *
     * @param array $ids //IDS
     * @param array $columns 字段 * 代表所有字段
     *
     * @return mixed
     */
    public function findByMany(array $ids = [], $columns = array('*'));

    /**
     * 根据条件查询单条数据
     * Find data by field and value
     *
     * @param string $field 字段
     * @param string $value 值
     * @param array $columns 字段 * 代表所有字段
     *
     * @return mixed
     */
    public function findByField(string $field, string $value, array $columns = array('*'));

    /**
     * 根据多属性条件单条查询
     *
     * @param array $attributes 多属性条件
     * @param array $columns 字段 * 代表所有字段
     *
     * @return mixed
     */
    public function findByAttributes(array $attributes = [], array $columns = array('*'));

    /**
     * 根据条件查询所有数据
     *
     * @param string $field 字段
     * @param string $value 值
     * @param array $columns 字段 * 代表所有字段
     *
     * @return mixed
     */
    public function getByField(string $field, string $value, array $columns = array('*'));

    /**
     * 根据多属性条件获取多条
     *
     * @param array $attributes 查询属性条件
     * @param array $columns 字段 * 代表所有字段
     *
     * @return mixed
     */
    public function getByAttributes(array $attributes = [], array $columns = array('*'));

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
    public function paginateByAttributes(array $attributes = [], int $perPage = 20, array $columns = array('*'), $method = 'full');
}
