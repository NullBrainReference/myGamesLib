<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Strategies\EntityListBehavior\ProjectListProcessor;
use Illuminate\Support\Facades\Schema;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::query();
        $processor = new ProjectListProcessor();

        $query = $processor->search($query, $request->input('search'));
        $query = $processor->applyExtraFilters($query, $request);
        $query = $processor->initialOrder($query, $request->input('sort', 'latest'));

        $projects = $query->paginate(10)->withQueryString();

        return view('projects.index', compact('projects'));
    }

    public function view(int $id)
    {
        $project = Project::with(['owners', 'editors', 'participants', 'mechanics'])->findOrFail($id);
        $allUsers = User::orderBy('name', 'asc')->get();

        return view('projects.view', compact('project', 'allUsers'));
    }

    public function contributions(int $id)
    {
        $project = Project::with(['owners', 'comment.user', 'comment.replies.user'])->findOrFail($id);

        return view('projects.contributions', compact('project'));
    }

    public function create(Request $request)
    {
        $commentId = $request->query('comment_id');
        return view('projects.create', compact('commentId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'      => 'required|string|max:255|min:3',
            'content'    => 'required|string',
            'comment_id' => 'nullable|exists:comments,id',
            'icon_big'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'icon_small' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:1024',
        ]);

        $data = [
            'title'      => $request->input('title'),
            'content'    => $request->input('content'),
            'is_public'  => $request->has('is_public'),
            'comment_id' => $request->input('comment_id'),
        ];

        if ($request->hasFile('icon_big')) {
            $data['icon_big'] = $request->file('icon_big')->store('projects/icons', 'public');
        }

        if ($request->hasFile('icon_small')) {
            $data['icon_small'] = $request->file('icon_small')->store('projects/icons', 'public');
        }

        $project = Project::create($data);

        // Создатель становится владельцем проекта
        $project->owners()->attach(Auth::id());

        return redirect()->route('projects.view', $project->id)
                         ->with('success', 'Project workspace created! Approve the project to sync roles.');
    }

    public function approve(int $id)
    {
        $project = Project::with(['owners', 'mechanics'])->findOrFail($id);

        if (!$project->owners->contains(Auth::id())) {
            abort(403, 'Unauthorized approval request.');
        }

        $participantIds = $project->owners->pluck('id')->toArray();

        if ($project->comment_id) {
            $originComment = Comment::with('replies')->find($project->comment_id);
            if ($originComment) {
                if ($originComment->user_id) {
                    $participantIds[] = $originComment->user_id;
                }

                $replyUserIds = $originComment->replies->pluck('user_id')->filter()->toArray();
                $participantIds = array_merge($participantIds, $replyUserIds);
            }
        }

        $project->participants()->sync(array_unique($participantIds));

        $editorIds = $project->mechanics->pluck('user_id')->filter()->unique()->toArray();
        $project->editors()->sync($editorIds);

        if (Schema::hasColumn('projects', 'is_approved')) {
            $project->update(['is_approved' => true]);
        }

        return redirect()->back()->with('success', 'Project approved! Participants and Editors synced automatically.');
    }

    public function edit(int $id)
    {
        $project = Project::with(['owners'])->findOrFail($id);

        if (!$project->owners->contains(Auth::id())) {
            abort(403, 'You are not a registered owner of this workspace blueprint.');
        }

        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, int $id)
    {
        $project = Project::findOrFail($id);

        if (!$project->owners->contains(Auth::id())) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'title'      => 'required|string|max:255|min:3',
            'content'    => 'required|string',
            'icon_big'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'icon_small' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:1024',
        ]);

        $data = [
            'title'     => $request->input('title'),
            'content'   => $request->input('content'),
            'is_public' => $request->has('is_public'),
        ];

        if ($request->hasFile('icon_big')) {
            if ($project->icon_big && Storage::disk('public')->exists($project->icon_big)) {
                Storage::disk('public')->delete($project->icon_big);
            }
            $data['icon_big'] = $request->file('icon_big')->store('projects/icons', 'public');
        }

        if ($request->hasFile('icon_small')) {
            if ($project->icon_small && Storage::disk('public')->exists($project->icon_small)) {
                Storage::disk('public')->delete($project->icon_small);
            }
            $data['icon_small'] = $request->file('icon_small')->store('projects/icons', 'public');
        }

        $project->update($data);

        return redirect()->route('projects.view', $project->id)
            ->with('success', 'Project architecture updated successfully.');
    }

    public function destroy(int $id)
    {
        $project = Project::findOrFail($id);

        if (!$project->owners->contains(Auth::id())) {
            abort(403);
        }

        if ($project->icon_big && Storage::disk('public')->exists($project->icon_big)) {
            Storage::disk('public')->delete($project->icon_big);
        }
        if ($project->icon_small && Storage::disk('public')->exists($project->icon_small)) {
            Storage::disk('public')->delete($project->icon_small);
        }

        $project->delete();

        return redirect()->route('projects')
                         ->with('success', 'Workspace record deleted permanently.');
    }

    public function attachEditor(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);
        if (!$project->owners->contains(Auth::id())) abort(403);

        $request->validate(['user_id' => 'required|exists:users,id']);

        $project->editors()->syncWithoutDetaching([$request->input('user_id')]);

        return redirect()->back()->with('success', 'Editor privileges assigned.');
    }

    public function detachEditor($projectId, $userId)
    {
        $project = Project::findOrFail($projectId);
        if (!$project->owners->contains(Auth::id())) abort(403);

        $project->editors()->detach($userId);

        return redirect()->back()->with('success', 'Editor access revoked.');
    }

    public function attachParticipant(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);
        if (!$project->owners->contains(Auth::id())) abort(403);

        $request->validate(['user_id' => 'required|exists:users,id']);

        $project->participants()->syncWithoutDetaching([$request->input('user_id')]);

        return redirect()->back()->with('success', 'Participant registered successfully.');
    }

    public function detachParticipant($projectId, $userId)
    {
        $project = Project::findOrFail($projectId);
        if (!$project->owners->contains(Auth::id())) abort(403);

        $project->participants()->detach($userId);

        return redirect()->back()->with('success', 'Participant removed from workspace.');
    }
}
