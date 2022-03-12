<?php

namespace App\Repositories;

abstract class ModelRepository
{

    /**
     * @var $model \Illuminate\Database\Eloquent\Model subclass
     */
    protected $model;

    /**
     * @param $model \Illuminate\Database\Eloquent\Model subclass
     */
    public function __construct($model=null)
    {
        $this->model = $model;
    }

    /**
     * @param $model \Illuminate\Database\Eloquent\Model object
     *
     * @return $this
     */
    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @param $method
     * @param $parameters
     *
     * Forward all method calls to \Illuminate\Database\Eloquent\Model
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array(array($this->model, $method), $parameters);
    }

    /**
     * Simple Update
     * @param  Integer $id primary key
     * @param  array $attributes  data
     * @return $model \Illuminate\Database\Eloquent\Model object
     */
    public function updateById($id, $attributes)
    {
        $obj = $this->model->find($id);
        $obj->update($attributes);
        return $obj;
    }

    /**
     * Simple Delete
     * @param  Integer $id Primary Key
     * @return Boolean
     */
    public function deleteById($id)
    {
        return $this->model->find($id)->delete();
    }

    /**
     * Find By Column
     * @param  string $column
     * @param  mixed $value
     * @return $model \Illuminate\Database\Eloquent\Model object
     */
    public function findByColumn($column, $value)
    {
        return $this->model->where($column, $value)->first();
    }
}
