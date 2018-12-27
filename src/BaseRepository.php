<?php
namespace Clockwork\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Clockwork\Base\Traits\Transaction;

abstract class BaseRepository
{
    use Transaction;

    protected $request;

    /**
     * Get a item from the database
     *
     * @param object $request
     * @return item
     */
    public function find(object $request)
    {
        return $this->itemResponse(
            $request,
            $this->model->where('id', $request->id)
        );
    }

    /**
     * Get a collection from the database
     *
     * @param object $request
     * @return collection
     */
    public function getAll(object $request) 
    {
        return $this->collectionResponse(
            $request,
            $this->model
        );
    }

    /**
     * Get an item and it's relations
     *
     * @param object $request
     * @param [type] $query
     * @return collection
     */
    public function itemResponse(object $request, object $query) 
    {
        $this->request = $request;

        return $query
            ->when($request->has('with'), [$this, 'with'])
            ->first();
    }

    /**
     * Get a collection with relations,
     * scopes, sorting and filtering.
     *
     * @param object $request
     * @param object $query
     * @return collection
     */
    public function collectionResponse(object $request, object $query) 
    {
        $this->request = $request;

        $query = $query
            ->when($request->has('filter'), [$this, 'filter'])
            ->when($request->has('with'), [$this, 'with'])
            ->when($request->has('scopes'), [$this, 'scopes'])
            ->when(
                $request->has('sort') && $request->sort !== null,
                /* if */   [$this, 'sort'],
                /* else */ [$this, 'noSort']
            );

        return $query->get();
    }

    /**
     * Filter a query
     *
     * @param Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function filter(Builder $query) 
    {
        return $query->search($this->request->filter);
    }

    /**
     * Load the relationships
     *
     * @param Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function with(Builder $query) 
    {
        return $query->with(
            explode(',', $this->request->with)
        );
    }

    /**
     * Get the query scopes
     *
     * @param Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopes(Builder $query) 
    {
        $decoded = json_decode($this->request->scopes, true);

        if (json_last_error() == JSON_ERROR_NONE) {
            dd($decoded);
            $scopes = $decoded;
        } elseif (!is_object($decoded) && !is_array($decoded)) {
            $scopes = explode(',', $this->request->scopes);
        } else {
            $scopes = [];
        }

        return $query->scopes($scopes);
    }

    /**
     * Sort the query
     *
     * @param Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function sort(Builder $query) 
    {
        foreach (explode(',', $this->request->sort) as $sort) {
            list($sortCol, $sortDir) = explode('|', $sort);
            $query = $query->orderBy($sortCol, $sortDir);
        }

        return $query;
    }

    /**
     * Default sort method
     *
     * @param Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function noSort(Builder $query) 
    {
        return $query->orderBy('id', 'asc');
    }
}
