<?php

namespace App\Strategies\EntityListBehavior;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

interface IListProcessor
{
    public function search(Builder $query, ?string $term): Builder;

    public function initialOrder(Builder $query, string $sortType): Builder;

    public function applyExtraFilters(Builder $query, Request $request): Builder;
}
