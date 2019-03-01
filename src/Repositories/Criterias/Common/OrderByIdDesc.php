<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 2019-03-01
 * Time: 21:05
 */

namespace Zhyu\Repositories\Criterias\Common;

use Zhyu\Repositories\Contracts\RepositoryInterface;
use Zhyu\Repositories\Criterias\Criteria;


class OrderByIdDesc extends Criteria
{
    public function apply($model, RepositoryInterface $repository)
    {
        $query = $model->orderby('id', 'desc');
        return $query;
    }

}