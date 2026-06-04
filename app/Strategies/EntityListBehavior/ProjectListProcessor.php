<?php

namespace App\Strategies\EntityListBehavior;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ProjectListProcessor implements IListProcessor
{
    public function search(Builder $query, ?string $term): Builder
    {
        if (empty($term)) return $query;

        return $query->where('title', 'like', "%{$term}%");
    }

    public function initialOrder(Builder $query, string $sortType): Builder
    {
        return match ($sortType) {
            'title'  => $query->orderBy('title', 'asc'),
            default  => $query->latest(), // Fallback latest strategy
        };
    }

    public function applyExtraFilters(Builder $query, Request $request): Builder
    {
        // Filter out locked threads if requested via checkbox
        if ($request->has('hide_locked')) {
            $query->where('is_locked', false);
        }

        return $query;
    }
}
