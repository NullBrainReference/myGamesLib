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
    public function storeForProject(Request $request, $project_id)
    {
        $project = Project::findOrFail($project_id);

        $validated = $request->validate([
            'title'      => 'required|string|max:255',
            'content'    => 'required|string',
            'comment_id' => 'required|exists:comments,id',
        ]);

        $approved = Auth::user()->isAdmin();

        DB::transaction(function () use ($validated, $project, $approved) {
            $mechanic = Mechanic::create([
                'title'      => $validated['title'],
                'content'    => $validated['content'],
                'approved'   => $approved,
                'user_id'    => Auth::id(),
                'comment_id' => $validated['comment_id'],
            ]);

            $project->mechanics()->attach($mechanic->mechanic_id);
        });

        return redirect()->back()->with('success', 'Mechanic proposal successfully submitted!');
    }

    public function storeForGame(Request $request, $game_id)
    {
        $game = Game::where('game_id', $game_id)->firstOrFail();

        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $approved = Auth::user()->isAdmin();

        DB::transaction(function () use ($validated, $game, $approved) {
            $mechanic = Mechanic::create([
                'title'    => $validated['title'],
                'content'  => $validated['content'],
                'approved' => $approved,
                'user_id'  => Auth::id(),
            ]);

            $game->mechanics()->attach($mechanic->mechanic_id);
        });

        return redirect()->back()->with('success', 'Game mechanic created successfully!');
    }

    public function storeProposedFromComment(Request $request, Comment $comment)
    {
        // dd($request->all(), $comment);

        $validated = $request->validate([
            'mechanic_title' => ['required', 'string', 'max:255'],
            'content'        => ['required', 'string', 'max:3000'],
        ]);

        DB::transaction(function () use ($validated, $comment) {
            $mechanic = Mechanic::create([
                'title'       => $validated['mechanic_title'],
                'content' => $validated['content'],
                'user_id'     => auth()->id(),
                'comment_id'  => $comment->id,
                'status'      => 'proposed',
            ]);

            Comment::create([
                'user_id'          => auth()->id(),
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
            ->with('game:game_id,title'); // Подгружаем связанную игру для бэйджа

        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        if ($rawQuery !== '') {
            if (str_contains($rawQuery, ':')) {
                // Разделяем запрос по синтаксису mechanic:game
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
                // Обычный поиск по названию механики ИЛИ игры
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