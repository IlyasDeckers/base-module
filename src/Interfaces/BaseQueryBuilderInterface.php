<?php
namespace Clockwork\Base\Interfaces;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

interface BaseQueryBuilderInterface
{
    public function itemResponse(object $query) : object;

    public function collectionResponse(object $query) : Collection;

    public function filter(Builder $query) : Builder;

    public function with(Builder $query) : Builder;

    public function scopes(Builder $query) : Builder;

    public function sort(Builder $query) : Builder;

    public function noSort(Builder $query) : Builder;

    public function groupBy(Builder $query) : Builder;
}
