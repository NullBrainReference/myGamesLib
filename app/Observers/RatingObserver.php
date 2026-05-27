<?php

namespace App\Observers;

use App\Models\Rating;
use Illuminate\Support\Facades\DB;

class RatingObserver
{
    public function created(Rating $rating)
    {
        $this->recalculateAggregates($rating->game_id);
    }

    public function updated(Rating $rating)
    {
        $this->recalculateAggregates($rating->game_id);
    }

    public function deleted(Rating $rating)
    {
        $this->recalculateAggregates($rating->game_id);
    }

    protected function recalculateAggregates($gameId)
    {
        DB::transaction(function () use ($gameId) {
            $stats = DB::table('ratings')
                ->selectRaw('COUNT(*) as cnt, COALESCE(AVG(rating), 0) as avg')
                ->where('game_id', $gameId)
                ->first();

            DB::table('games')
                ->where('game_id', $gameId)
                ->update([
                    'ratings_count' => (int) $stats->cnt,
                    'average_rating' => round((float) $stats->avg, 1),
                ]);
        });
    }
}
