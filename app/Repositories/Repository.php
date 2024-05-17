<?php

namespace App\Repositories;

class Repository
{
    protected $searchable = [];
    protected $filterable = [];
    protected $related = [];
    protected $query = null;
    protected $model;

    /**
     * Create a new class instance.
     */
    public function __construct($model)
    {
        $this->model = new $model;
        $this->query = $this->model->query();
    }

    /**
     * Get all records.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        $limit = request()->get('per_page', 10);
        $page = request()->get('page', 1);

        if(count($this->searchable) > 0 && request()->has('search')){
            $search = request()->get('search');
            $this->query->where(function($query) use ($search){
                foreach($this->searchable as $field){
                    $query->orWhere($field, 'like', "%$search%");
                }
            });
        }

        if(request()->has('order_by') || request()->has('order')){
            $order = request()->get('order_by', 'id');
            $direction = request()->get('order', 'asc');
            $this->query->orderBy($order, $direction);
        }

        if(request()->has('with')){
            $with = request()->get('with');
            if(is_array($with)){
                $this->query->with($with);
            } else {
                $with = explode(',', request()->get('with'));
                $this->query->with($with);
            }
        }

        if(count($this->filterable) > 0){
            foreach($this->filterable as $field){
                if(request()->has($field)){
                    $this->query->where($field, request()->get($field));
                }
            }
        }

        if(request()->get('no_paginate', false)){
            return $this->query->get();
        }

        $results = $this->query->paginate($limit, ['*'], 'page', $page);
        return $this->removePathsFromResult($results);
    }

    /**
     * Create a new model instance
     * 
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data)
    {
        $instance = $this->query->create($data);
        return $instance;
    }

    /**
     * Update a model instance
     * 
     * @param mixed $id
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model|false
     */
    public function update($id, array $data)
    {
        $instance = $this->find($id);
        if($instance){
            $instance->update($data);
            return $instance;
        }

        return false;
    }

    /**
     * Delete a model instance
     * 
     * @param mixed $id
     * @return \Illuminate\Database\Eloquent\Model|false
     */
    public function delete($id)
    {
        $instance = $this->find($id);
        if($instance){
            $instance->delete();
            return $instance;
        }

        return false;
    }

    /**
     * Delete all model instance
     */
    public function deleteAll()
    {
        return $this->query->delete();
    }

    /**
     * Find a model instance
     * 
     * @param mixed $id
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function find($id)
    {
        if(request()->has('with')){
            $with = request()->get('with');
            if(is_array($with)){
                $this->query->with($with);
            } else {
                $with = explode(',', request()->get('with'));
                $this->query->with($with);
            }
        }
        
        return $this->query->find($id);
    }

    /**
     * Return the current query object
     * 
     * @return \Illuminate\Database\Eloquent\Builder|null
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Load passed relations
     * 
     * @param array $relations
     * @return \App\Repositories\Repository
     */
    public function with($relations)
    {
        foreach($relations as $relation){
            if(in_array($relation, $this->related)){
                $this->query->with($relation);
            }
        }
        
        return $this;
    }

    protected function removePathsFromResult($queryResult)
    {
        $result = $queryResult->toArray();

        $keys = [
            'first_page_url', 'last_page_url', 'links',
            'next_page_url', 'path', 'prev_page_url',
        ];

        foreach ($keys as $key) {
            if (isset($result[$key])) {
                unset($result[$key]);
            }
        }

        return $result;
    }
}
