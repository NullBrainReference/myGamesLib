<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Mechanic;
use App\Models\Notification;
use App\Models\Project;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class MechanicController extends Controller
{
    public function storeForGame(Request $request, $game_id)
    {
        $game = Game::where('game_id', $game_id)->firstOrFail();

        $validated = $request->validate([
            'title'      => 'required|string|max:255',
            'content'    => 'required|string',
            'parent_id'  => 'nullable|exists:mechanics,mechanic_id',
            'comment_id' => 'nullable|exists:comments,id',
        ]);

        $approved = Auth::user()->isAdmin();

        DB::transaction(function () use ($validated, $game, $approved) {
            Mechanic::create([
                'title'      => $validated['title'],
                'content'    => $validated['content'],
                'game_id'    => $game->game_id,
                'parent_id'  => $validated['parent_id'] ?? null,
                'comment_id' => $validated['comment_id'] ?? null,
                'approved'   => $approved,
                'user_id'    => Auth::id(),
            ]);
        });

        return redirect()->back()->with('success', 'Game mechanic submitted successfully!');
    }

    public function storeForProject(Request $request, $project_id)
    {
        $project = Project::findOrFail($project_id);

        $validated = $request->validate([
            'title'      => 'required|string|max:255',
            'content'    => 'required|string',
            'parent_id'  => 'nullable|exists:mechanics,mechanic_id',
            'comment_id' => 'nullable|exists:comments,id',
        ]);

        $approved = Auth::user()->isAdmin();

        DB::transaction(function () use ($validated, $project, $approved) {
            Mechanic::create([
                'title'      => $validated['title'],
                'content'    => $validated['content'],
                'project_id' => $project->id,
                'parent_id'  => $validated['parent_id'] ?? null,
                'comment_id' => $validated['comment_id'] ?? null,
                'approved'   => $approved,
                'user_id'    => Auth::id(),
            ]);
        });

        return redirect()->back()->with('success', 'Project mechanic proposal successfully submitted!');
    }

    public function storeProposedFromComment(Request $request, Comment $comment)
    {
        $validated = $request->validate([
            'title'      => 'required|string|max:255',
            'content'    => 'required|string|max:3000',
            'parent_id'  => 'nullable|exists:mechanics,mechanic_id',
            'game_id'    => 'nullable|exists:games,game_id',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $projectId = $validated['project_id']
            ?? $comment->project?->id
            ?? ($comment->commentable_type === \App\Models\Project::class ? $comment->commentable_id : null);

        $approved = Auth::user()->isAdmin();

        DB::transaction(function () use ($validated, $comment, $approved, $projectId) {
            $newComment = Comment::create([
                'user_id'          => Auth::id(),
                'content'          => "⚙️ **Proposed Mechanic: {$validated['title']}**\n\n{$validated['content']}",
                'commentable_type' => $comment->commentable_type,
                'commentable_id'   => $comment->commentable_id,
                'parent_id'        => $comment->id,
            ]);

            Mechanic::create([
                'title'      => $validated['title'],
                'content'    => $validated['content'],
                'user_id'    => Auth::id(),
                'comment_id' => $newComment->id,
                'parent_id'  => $validated['parent_id'] ?? null,
                'game_id'    => $validated['game_id'] ?? null,
                'project_id' => $projectId,
                'approved'   => $approved,
            ]);
        });

        return redirect()->back()->with('success', 'Mechanic proposal submitted successfully!');
    }

    public function update(Request $request, $mechanic_id)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $mechanic = Mechanic::where('mechanic_id', $mechanic_id)->firstOrFail();

        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $mechanic->update($validated);

        return redirect()->back()->with('success', 'Mechanic updated successfully.');
    }

    public function destroy($mechanic_id)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $mechanic = Mechanic::where('mechanic_id', $mechanic_id)->firstOrFail();
        $mechanic->delete();

        return redirect()->back()->with('success', 'Mechanic deleted successfully.');
    }

    public function search(Request $request): JsonResponse
    {
        $rawQuery = trim($request->input('query', ''));
        $projectId = $request->input('project_id');

        $query = Mechanic::query()
            ->approved()
            ->canonical()
            ->with('game:game_id,title');

        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        if ($rawQuery !== '') {
            if (str_contains($rawQuery, ':')) {
                [$mechanicTerm, $gameTerm] = array_map('trim', explode(':', $rawQuery, 2));

                if ($mechanicTerm !== '') {
                    $query->where('title', 'LIKE', "%{$mechanicTerm}%");
                }

                if ($gameTerm !== '') {
                    $query->whereHas('game', function ($g) use ($gameTerm) {
                        $g->where('title', 'LIKE', "%{$gameTerm}%");
                    });
                }
            } else {
                $query->where(function ($q) use ($rawQuery) {
                    $q->where('title', 'LIKE', "%{$rawQuery}%")
                      ->orWhereHas('game', function ($g) use ($rawQuery) {
                          $g->where('title', 'LIKE', "%{$rawQuery}%");
                      });
                });
            }
        }

        $mechanics = $query->limit(15)->get(['mechanic_id', 'title', 'content', 'game_id', 'project_id']);

        return response()->json($mechanics);
    }

        /**
     * Approve a pending mechanic or variant.
     */
    public function approve($mechanic_id)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $mechanic = Mechanic::with(['user', 'project', 'game'])
            ->where('mechanic_id', $mechanic_id)
            ->firstOrFail();

        $mechanic->update(['approved' => true]);

        // Send notification to the author who proposed the mechanic
        if ($mechanic->user_id && $mechanic->user_id !== Auth::id()) {
            // Resolve parent entity for navigation target_url (Project or Game)
            $notifiable = $mechanic->project ?? $mechanic->game;

            Notification::send(
                userId: $mechanic->user_id,
                type: $mechanic->project_id ? 'project' : 'mechanic',
                title: 'Mechanic Proposal Approved! ⚙️',
                message: 'Your proposed mechanic "' . $mechanic->title . '" has been approved and published.',
                notifiable: $notifiable
            );
        }

        return back()->with('success', 'Mechanic proposal approved successfully!');
    }

    /**
     * Reject / Decline a pending mechanic proposal.
     */
    public function reject($mechanic_id)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $mechanic = Mechanic::with(['user', 'project', 'game'])
            ->where('mechanic_id', $mechanic_id)
            ->firstOrFail();

        // Notify author before soft/hard deleting the proposal
        if ($mechanic->user_id && $mechanic->user_id !== Auth::id()) {
            $notifiable = $mechanic->project ?? $mechanic->game;

            Notification::send(
                userId: $mechanic->user_id,
                type: $mechanic->project_id ? 'project' : 'mechanic',
                title: 'Mechanic Proposal Declined',
                message: 'Your proposed mechanic "' . $mechanic->title . '" was declined by an administrator.',
                notifiable: $notifiable
            );
        }

        $mechanic->delete();

        return back()->with('success', 'Mechanic proposal rejected.');
    }

    /**
     * Batch process pending mechanic proposals based on community voting rules:
     * - Minimum 3 total votes on the proposal comment
     * - At least 75% positive upvotes
     */
    public function processBatchMechanicApprovals()
    {
        // Fetch all pending mechanics linked to a comment thread with votes
        $pendingMechanics = Mechanic::where('approved', false)
            ->whereNotNull('comment_id')
            ->with(['comment.votes', 'user', 'project', 'game'])
            ->get();

        $approvedCount = 0;
        $rejectedCount = 0;

        foreach ($pendingMechanics as $mechanic) {
            $comment = $mechanic->comment;
            if (!$comment) {
                continue;
            }

            // 1. Calculate voting metrics from the comment
            $totalVotes = $comment->votes->count();
            $upvotes = $comment->votes->where('vote', '>', 0)->count();

            // 2. Evaluate criteria: total votes >= 3
            if ($totalVotes >= 3) {
                $upvoteRate = ($upvotes / $totalVotes) * 100;

                if ($upvoteRate >= 75) {
                    // APPROVE: Rate is >= 75%
                    DB::transaction(function () use ($mechanic) {
                        $mechanic->update(['approved' => true]);

                        if ($mechanic->user_id) {
                            $notifiable = $mechanic->project ?? $mechanic->game;

                            Notification::send(
                                userId: $mechanic->user_id,
                                type: $mechanic->project_id ? 'project' : 'mechanic',
                                title: 'Mechanic Approved by Community! ⚙️',
                                message: 'Your proposed mechanic "' . $mechanic->title . '" reached the community vote threshold and was automatically published.',
                                notifiable: $notifiable
                            );
                        }
                    });

                    $approvedCount++;
                } else {
                    // REJECT & REMOVE: Votes >= 3 but upvote rate < 75%
                    DB::transaction(function () use ($mechanic) {
                        if ($mechanic->user_id) {
                            $notifiable = $mechanic->project ?? $mechanic->game;

                            Notification::send(
                                userId: $mechanic->user_id,
                                type: $mechanic->project_id ? 'project' : 'mechanic',
                                title: 'Mechanic Declined by Community',
                                message: 'Your proposed mechanic "' . $mechanic->title . '" received sufficient votes but failed to reach the 75% approval threshold and was removed.',
                                notifiable: $notifiable
                            );
                        }

                        // Delete the failed proposal
                        $mechanic->delete();
                    });

                    $rejectedCount++;
                }
            }
        }

        return back()->with('success', "Batch evaluation completed! Approved {$approvedCount} and removed {$rejectedCount} mechanic proposal(s) based on community votes.");
    }
}
