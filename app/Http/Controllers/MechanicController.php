<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Mechanic;
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
                'game_id'    => $game->game_id, // Прямая привязка к игре
                'parent_id'  => $validated['parent_id'] ?? null, // Если создается вариант
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
                'project_id' => $project->id, // Прямая привязка к проекту
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

        $approved = Auth::user()->isAdmin();

        DB::transaction(function () use ($validated, $comment, $approved) {
            $mechanic = Mechanic::create([
                'title'      => $validated['title'],
                'content'    => $validated['content'],
                'user_id'    => Auth::id(),
                'comment_id' => $comment->id,
                'parent_id'  => $validated['parent_id'] ?? null,
                'game_id'    => $validated['game_id'] ?? null,
                'project_id' => $validated['project_id'] ?? null,
                'approved'   => $approved,
            ]);

            Comment::create([
                'user_id'          => Auth::id(),
                'content'          => "⚙️ **Proposed Mechanic: {$mechanic->title}**\n\n{$validated['content']}",
                'commentable_type' => $comment->commentable_type,
                'commentable_id'   => $comment->commentable_id,
                'parent_id'        => $comment->id,
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
}