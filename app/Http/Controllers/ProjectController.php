<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Strategies\EntityListBehavior\ProjectListProcessor;

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
        $project = Project::with(['owners', 'editors', 'participants'])->findOrFail($id);

        $allUsers = User::orderBy('name', 'asc')->get();

        return view('projects.view', compact('project', 'allUsers'));
    }

    public function create()
    {
        $users = User::orderBy('name', 'asc')->get();
        return view('projects.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:255|min:3',
            'content' => 'required|string',
        ]);

        $project = Project::create([
            'title'     => $request->input('title'),
            'content'   => $request->input('content'),
            'is_public' => $request->has('is_public'),
        ]);

        $project->owners()->attach(Auth::id());

        $project->editors()->sync($request->input('editors', []));
        $project->participants()->sync($request->input('participants', []));

        return redirect()->route('projects.view', $project->id)
                         ->with('success', 'Project workspace created successfully!');
    }

    public function edit(int $id)
    {
        $project = Project::with(['owners', 'editors', 'participants'])->findOrFail($id);

        if (!$project->owners->contains(Auth::id())) {
            abort(403, 'You are not a registered owner of this workspace blueprint.');
        }

        $users = User::orderBy('name', 'asc')->get();
        return view('projects.edit', compact('project', 'users'));
    }

    public function update(Request $request, int $id)
    {
        $project = Project::findOrFail($id);

        if (!$project->owners->contains(Auth::id())) {
            abort(403);
        }

        $request->validate([
            'title'   => 'required|string|max:255|min:3',
            'content' => 'required|string',
        ]);

        $project->update([
            'title'     => $request->input('title'),
            'content'   => $request->input('content'),
            'is_public' => $request->has('is_public'),
        ]);

        $project->editors()->sync($request->input('editors', []));
        $project->participants()->sync($request->input('participants', []));

        return redirect()->route('projects.view', $project->id)
                         ->with('success', 'Project architecture updated.');
    }

    public function destroy(int $id)
    {
        $project = Project::findOrFail($id);

        if (!$project->owners->contains(Auth::id())) {
            abort(403);
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
