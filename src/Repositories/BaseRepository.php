<?php

namespace Ecoflow\Core\Repositories;

use Illuminate\Database\Eloquent\Model;

class BaseRepository
{

    /**
     * $model
     *
     * @var Model
     */
    public $model;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * all
     *
     * @return mixed
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * find
     *
     * @param mixed $match
     * @return mixed
     */
    public function find($match)
    {
        if (is_array($match)) {
            $model = $this->model->where(key($match), $match[key($match)])->get();
            return $this->singularize($model);
        } else {
            return $model = $this->model->find($match);
        }
    }

    /**
     * create
     *
     * @param array $data
     * @return void
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * modify
     *
     * @param integer $id
     * @param array $data
     * @return void
     */
    public function modify(string $id, array $data): bool
    {
        return $this->model->find($id)->update($data);
    }

    /**
     * delete
     *
     * @param string $id
     * @return void
     */
    public function delete(string $id): bool
    {
        return $this->model->find($id)->delete();
    }

    /**
     * Helper function: if a collection has only 1 element we return the first
     *
     * @param collection $collection
     * @return mix
     */
    private function singularize($collection)
    {
        if (count($collection) === 1) return $collection->first();
        return $collection;
    }
}
