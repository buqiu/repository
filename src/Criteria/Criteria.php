<?php

namespace Buqiu\Repository\Criteria;

use Illuminate\Database\Eloquent\Model;
use Buqiu\Repository\Contracts\RepositoryInterface;

abstract class Criteria
{
    /**
     * @param Model $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public abstract function apply($model, RepositoryInterface $repository);
}
