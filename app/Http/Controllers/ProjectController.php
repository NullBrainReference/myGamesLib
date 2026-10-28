<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Comment;
use App\Models\User;
use App\Models\Notification;
use App\Models\ProjectApproveRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Strategies\EntityListBehavior\ProjectListProcessor;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

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

        if ($commentId) {
            $comment = Comment::with(['project', 'parent', 'replies.project'])->findOrFail($commentId);

            if ($comment->branchHasProject()) {
                return redirect()->back()->with('error', 'A project has already been created for this comment thread.');
            }
        }

        return view('projects.create', compact('commentId'));
    }

    public function fork(Project $project)
    {
        if (!$project->is_approved) {
            return back()->with('error', 'Only approved projects can be forked.');
        }

        // Duplicate project icons if present
        $newIconBig = null;
        if ($project->icon_big && Storage::disk('public')->exists($project->icon_big)) {
            $ext = pathinfo($project->icon_big, PATHINFO_EXTENSION);
            $newIconBig = 'projects/icons/' . Str::random(40) . '.' . $ext;
            Storage::disk('public')->copy($project->icon_big, $newIconBig);
        }

        $newIconSmall = null;
        if ($project->icon_small && Storage::disk('public')->exists($project->icon_small)) {
            $ext = pathinfo($project->icon_small, PATHINFO_EXTENSION);
            $newIconSmall = 'projects/icons/' . Str::random(40) . '.' . $ext;
            Storage::disk('public')->copy($project->icon_small, $newIconSmall);
        }

        // Create the forked project copy
        $forkedProject = Project::create([
            'title'             => $project->title . ' (Fork)',
            'content'           => $project->content,
            'is_public'         => true,
            'is_approved'       => false, // New fork starts as unapproved workspace
            'icon_big'          => $newIconBig,
            'icon_small'        => $newIconSmall,
            'comment_id'        => null,
            'parent_project_id' => $project->id,
        ]);

        // Attach current user as the owner of the fork
        $forkedProject->owners()->attach(auth()->id());

        // Copy approved mechanics from parent project
        $approvedMechanics = $project->mechanics()->where('approved', true)->get();
        foreach ($approvedMechanics as $mech) {
            $forkedProject->mechanics()->create([
                'title'      => $mech->title,
                'content'    => $mech->content,
                'approved'   => true,
                'user_id'    => auth()->id(),
                'comment_id' => null,
            ]);
        }

        // Notify owners of the original project
        foreach ($project->owners as $owner) {
            if ($owner->id !== auth()->id()) {
                Notification::send(
                    userId: $owner->id,
                    type: 'project',
                    title: 'Project Forked',
                    message: auth()->user()->name . ' created a fork of your project "' . $project->title . '".',
                    notifiable: $forkedProject
                );
            }
        }

        return redirect()
            ->route('projects.view', $forkedProject->id)
            ->with('success', 'Project forked successfully! You are now the owner of this workspace copy.');
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

        if ($request->filled('comment_id')) {
            $comment = Comment::with(['project', 'parent', 'replies.project'])->findOrFail($request->input('comment_id'));

            if ($comment->branchHasProject()) {
                return redirect()->back()->with('error', 'A project has already been created for this comment thread.');
            }
        }

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

        $project->owners()->attach(Auth::id());

        return redirect()
            ->route('projects.view', $project->id)
            ->with('success', 'Project workspace created! Approve the project to sync roles.');
    }

    public function requestApproval(Request $request, Project $project)
    {
        $user = auth()->user();

        // 1. Authorization Guard
        $isAuthor = $request->filled('comment_id') && Comment::where('id', $request->input('comment_id'))->where('user_id', $user->id)->exists();
        $isOwner  = $project->owners->contains($user->id);

        if (!$user->isAdmin() && !$isOwner && !$isAuthor) {
            return back()->with('error', 'Unauthorized to request approval for this project.');
        }

        // 2. Check if project is already approved
        if ($project->is_approved) {
            return back()->with('error', 'This project has already been approved.');
        }

        // 3. Entity Check: Disallow creation if an open/pending request already exists
        if ($project->hasPendingApproveRequest()) {
            return back()->with('error', 'An approval request is already open and pending voting for this project.');
        }

        // 4. Create comment and ProjectApproveRequest atomically
        DB::transaction(function () use ($request, $project, $user) {
            $parentCommentId = $request->input('comment_id');
            $parentComment   = Comment::find($parentCommentId);

            $comment = Comment::create([
                'post_id'    => $parentComment ? $parentComment->post_id : null,
                'parent_id'  => $parentCommentId,
                'user_id'    => $user->id,
                'project_id' => $project->id,
                'content'    => "🗳️ **PROJECT APPROVAL REQUEST**: Upvote this comment to approve project '{$project->title}'!",
            ]);

            ProjectApproveRequest::create([
                'project_id' => $project->id,
                'user_id'    => $user->id,
                'comment_id' => $comment->id,
                'status'     => 'pending',
            ]);
        });

        return back()->with('success', 'Project approval request submitted successfully! Community voting is now open.');
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

        return redirect()
            ->back()
            ->with('success', 'Project approved! Participants and Editors synced automatically.');
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
