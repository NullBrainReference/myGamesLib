<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Vote;

class VoteController extends Controller
{
    protected const APPROVAL_THRESHOLD = 5; 

    public function voteComment(Request $request, Comment $comment)
    {
        $request->validate(['type' => 'required|in:up,down']);
        $value = $request->type === 'up' ? 1 : -1;
        $userId = auth()->id();

        $existingVote = $comment->votes()->where('user_id', $userId)->first();

        if ($existingVote) {
            if ($existingVote->vote === $value) {
                $existingVote->delete();
            } else {
                $existingVote->update(['vote' => $value]);
            }
        } else {
            $comment->votes()->create([
                'user_id' => $userId,
                'vote' => $value,
            ]);
        }

        if ($comment->mechanic) {
            $netScore = $comment->score;
            
            if ($netScore >= self::APPROVAL_THRESHOLD && !$comment->mechanic->approved) {
                $comment->mechanic->update(['approved' => true]);
            } elseif ($netScore < self::APPROVAL_THRESHOLD && $comment->mechanic->approved) {
                $comment->mechanic->update(['approved' => false]);
            }
        }

        return back();
    }
}