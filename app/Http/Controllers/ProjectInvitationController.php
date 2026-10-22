<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Project;
use App\Models\ProjectInvitation;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectInvitationController extends Controller
{
    // Search community users and show invite interface with pagination
    public function create(Request $request, Project $project)
    {
        if (!auth()->user()->isAdmin() && !$project->owners->contains(auth()->id())) {
            abort(403, 'Unauthorized.');
        }

        $search = $request->input('search');

        $users = User::when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('name', 'asc')
            ->paginate(12)
            ->withQueryString();

        // Team and invite status sets
        $ownerIds       = $project->owners->pluck('id')->toArray();
        $editorIds      = $project->editors->pluck('id')->toArray();
        $participantIds = $project->participants->pluck('id')->toArray();

        $pendingInviteUserIds = $project->invitations()
            ->where('status', 'pending')
            ->pluck('user_id')
            ->toArray();

        return view('projects.invite-search', compact(
            'project',
            'users',
            'search',
            'ownerIds',
            'editorIds',
            'participantIds',
            'pendingInviteUserIds'
        ));
    }

    // Send invitation to a user
    public function store(Request $request, Project $project)
    {
        if (!auth()->user()->isAdmin() && !$project->owners->contains(auth()->id())) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role'    => 'required|in:editor,participant',
        ]);

        if ($project->owners->contains($validated['user_id']) ||
            $project->editors->contains($validated['user_id']) ||
            $project->participants->contains($validated['user_id'])) {
            return back()->with('error', 'User is already a team member of this project.');
        }

        $existing = ProjectInvitation::where('project_id', $project->id)
            ->where('user_id', $validated['user_id'])
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return back()->with('error', 'A pending invitation has already been sent to this user.');
        }

        $invitation = ProjectInvitation::create([
            'project_id' => $project->id,
            'user_id'    => $validated['user_id'],
            'inviter_id' => auth()->id(),
            'role'       => $validated['role'],
            'status'     => 'pending',
        ]);

        Notification::send(
            userId: $invitation->user_id,
            type: 'project_invitation',
            title: 'New Project Invitation',
            message: auth()->user()->name . ' invited you to join "' . $project->title . '" as an ' . ucfirst($invitation->role) . '.',
            notifiable: $invitation
        );

        return back()->with('success', 'Invitation sent successfully!');
    }

    // List invitations for current user
    public function index()
    {
        $invitations = auth()->user()->projectInvitations()
            ->with(['project', 'inviter'])
            ->paginate(10);

        return view('projects.invitations', compact('invitations'));
    }

    // Accept invitation
    public function accept(ProjectInvitation $invitation)
    {
        if ($invitation->user_id !== auth()->id()) {
            abort(403, 'Unauthorized.');
        }

        if ($invitation->status !== 'pending') {
            return back()->with('error', 'This invitation has already been processed.');
        }

        $invitation->update(['status' => 'accepted']);

        if ($invitation->role === 'editor') {
            $invitation->project->editors()->syncWithoutDetaching([auth()->id()]);
        } else {
            $invitation->project->participants()->syncWithoutDetaching([auth()->id()]);
        }

        Notification::send(
            userId: $invitation->inviter_id,
            type: 'project',
            title: 'Invitation Accepted',
            message: auth()->user()->name . ' accepted your invitation to join "' . $invitation->project->title . '" as ' . ucfirst($invitation->role) . '.',
            notifiable: $invitation->project
        );

        return back()->with('success', 'You joined project "' . $invitation->project->title . '" as ' . ucfirst($invitation->role) . '!');
    }

    // Decline invitation
    public function decline(ProjectInvitation $invitation)
    {
        if ($invitation->user_id !== auth()->id()) {
            abort(403, 'Unauthorized.');
        }

        if ($invitation->status !== 'pending') {
            return back()->with('error', 'This invitation has already been processed.');
        }

        $invitation->update(['status' => 'declined']);

        return back()->with('success', 'Invitation declined.');
    }
}
