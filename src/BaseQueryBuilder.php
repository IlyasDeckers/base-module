<?php
namespace IlyasDeckers\BaseModule;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use IlyasDeckers\BaseModule\Interfaces\BaseQueryBuilderInterface;

abstract class BaseQueryBuilder implements BaseQueryBuilderInterface
{
    /**
     * Get an item and it's relations
     *
     * @param object $request
     * @param [type] $query
     * @return collection
     */
    public function itemResponse(object $query) : object
    {
        return $query
            ->when(request()->has('with'), [$this, 'with'])
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
    public function collectionResponse(object $query) : Collection
    {
        $query = $query
            ->when(request()->has('filter'), [$this, 'filter'])
            ->when(request()->has('with'), [$this, 'with'])
            ->when(request()->has('scopes'), [$this, 'scopes'])
            ->when(
                request()->has('sort') && $request->sort !== null,
                /* if */   [$this, 'sort'],
                /* else */ [$this, 'noSort']
            );

        if (request()->paginate == 'true') {
            return $query->paginate(request()->per_page);
        }

        return $query->get()
            ->when(request()->has('groupBy'), [$this, 'groupBy']);
    }

    /**
     * Filter a query
     *
     * @param Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function filter(Builder $query) : Builder
    {
        return $query->search(
            request()->filter
        );
    }

    /**
     * Load the relationships
     *
     * @param Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function with(Builder $query) : Builder
    {
        return $query->with(
            explode(',', request()->with)
        );
    }

    /**
     * Get the query scopes
     *
     * @param Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopes(Builder $query) : Builder
    {
        $decoded = json_decode(request()->scopes, true);

        if (json_last_error() == JSON_ERROR_NONE) {
            dd($decoded);
            $scopes = $decoded;
        } elseif (!is_object($decoded) && !is_array($decoded)) {
            $scopes = explode(',', request()->scopes);
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
    public function sort(Builder $query) : Builder
    {
        foreach (explode(',', request()->sort) as $sort) {
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
    public function noSort(Builder $query) : Builder
    {
        return $query->orderBy('id', 'asc');
    }

    /**
     * GroupBy
     * 
     * @param Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function groupBy(Builder $query) : Builder
    {
        return $query->groupBy(request()->groupBy);
    }
}
