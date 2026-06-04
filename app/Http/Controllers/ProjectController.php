<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
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
        // $thread = Thread::with('user')->findOrFail($id);

        // $comments = $thread->comments()
        //                 ->with('user')
        //                 ->latest()
        //                 ->paginate(10);

        return view('projects.view', compact('project'));
    }
}
