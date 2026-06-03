<?php

namespace App\Strategies\EntityListBehavior;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class GameListProcessor implements IListProcessor
{
    public function search(Builder $query, ?string $term): Builder
    {
        if (empty($term)) return $query;

        // Games search both titles and structural descriptions
        return $query->where(function($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }

    public function initialOrder(Builder $query, string $sortType): Builder
    {
        return match ($sortType) {
            'alpha'  => $query->orderBy('title', 'asc'),
            'oldest' => $query->oldest(),
            default  => $query->latest(),
        };
    }

    public function applyExtraFilters(Builder $query, Request $request): Builder
    {
        // Filter games by an age restriction switch or tag parameter
        if ($request->has('tag_id')) {
            $query->whereHas('tags', function($q) use ($request) {
                $q->where('tags.tag_id', $request->input('tag_id'));
            });
        }

        return $query;
    }
}
