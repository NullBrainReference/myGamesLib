<?php

namespace App\Http\Controllers;

use App\Models\HirePosition;
use App\Models\HirePosTicketRequest;
use App\Models\PositionSkill;
use App\Models\PositionTicket;
use App\Models\Project;
use Illuminate\Http\Request;

class HirePositionController extends Controller
{
    // List all open vacancies for a project
    public function index(Project $project)
    {
        $positions = $project->hirePositions()
            ->with(['skills', 'tickets'])
            ->where('is_open', true)
            ->get();

        return view('projects.vacancies', compact('project', 'positions'));
    }

    // Create a new hiring position for a project
    public function store(Request $request, Project $project)
    {
        if (!auth()->user()->isAdmin() && !$project->owners->contains(auth()->id())) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'required|string',
            'tickets_amount' => 'required|integer|min:1',
            'skills'         => 'nullable|array',
            'skills.*'       => 'string|max:50',
        ]);

        $position = $project->hirePositions()->create([
            'title'          => $validated['title'],
            'description'    => $validated['description'],
            'tickets_amount' => $validated['tickets_amount'],
            'is_open'        => true,
        ]);

        // Sync global skills
        if (!empty($validated['skills'])) {
            $skillIds = collect($validated['skills'])->map(function ($skillName) {
                return PositionSkill::firstOrCreate(['title' => trim($skillName)])->id;
            });
            $position->skills()->sync($skillIds);
        }

        return back()->with('success', 'Hire position published successfully.');
    }

    // Submit an application ticket (Consumes 1 ticket slot)
    public function apply(Request $request, HirePosition $position)
    {
        // Guard 1: Check if position has available ticket capacity
        if (!$position->hasAvailableTickets()) {
            return back()->with('error', 'No application tickets remaining for this position. The project owner must request more tickets.');
        }

        // Guard 2: Prevent duplicate applications from same user
        $alreadyApplied = PositionTicket::where('position_id', $position->id)
            ->where('user_id', auth()->id())
            ->exists();

        if ($alreadyApplied) {
            return back()->with('error', 'You have already applied for this position.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|min:10|max:1000',
        ]);

        // Consume 1 ticket slot by creating application entry
        PositionTicket::create([
            'position_id' => $position->id,
            'user_id'     => auth()->id(),
            'reason'      => $validated['reason'],
            'expired'     => false,
            'success'     => null, // Pending project owner review
        ]);

        return back()->with('success', 'Application ticket submitted successfully!');
    }

    // List all applicant tickets submitted across all positions in a project
    public function applicants(Request $request, Project $project)
    {
        // Ensure user is project owner or admin
        if (!auth()->user()->isAdmin() && !$project->owners->contains(auth()->id())) {
            abort(403, 'Unauthorized to review applicants for this project.');
        }

        // Load project positions for the request modal & filter dropdown
        $project->load('hirePositions');

        // Query tickets across ALL positions in this project via hasManyThrough
        $query = $project->positionTickets()
            ->with(['position', 'user']);

        // Optional position filter from request query string
        if ($request->filled('position_id')) {
            $query->where('position_id', $request->input('position_id'));
        }

        $tickets = $query->latest()->paginate(15)->withQueryString();

        return view('projects.tickets', compact('project', 'tickets'));
    }

    // Mark ticket as expired
    public function expireTicket(PositionTicket $ticket)
    {
        $project = $ticket->position->project;

        if (!auth()->user()->isAdmin() && !$project->owners->contains(auth()->id())) {
            abort(403, 'Unauthorized.');
        }

        $ticket->update(['expired' => true]);

        return back()->with('success', 'Ticket marked as expired.');
    }

    // Accept / Reject application ticket
    public function reviewTicket(Request $request, PositionTicket $ticket)
    {
        $project = $ticket->position->project;

        if (!auth()->user()->isAdmin() && !$project->owners->contains(auth()->id())) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'action' => 'required|in:accept,decline',
            'reason' => 'required|string|min:5|max:1000',
        ]);

        $isAccepted = $request->input('action') === 'accept';

        $ticket->update([
            'success'     => $isAccepted,
            'explanation' => $request->input('reason'), // Stores review feedback without overwriting applicant's $ticket->reason pitch
        ]);

        if ($isAccepted) {
            $project->participants()->syncWithoutDetaching([$ticket->user_id]);
        }

        $statusText = $isAccepted ? 'accepted' : 'declined';
        return back()->with('success', "Application ticket #{$ticket->id} has been {$statusText}.");
    }

    // Request additional application ticket slots from Admin
    public function requestMoreTickets(Request $request, Project $project)
    {
        if (!auth()->user()->isAdmin() && !$project->owners->contains(auth()->id())) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'hire_position_id' => 'required|exists:hire_positions,id',
            'quantity'         => 'required|integer|min:1|max:50',
            'reason'           => 'required|string|min:10|max:1000',
        ]);

        $position = $project->hirePositions()->findOrFail($validated['hire_position_id']);

        HirePosTicketRequest::create([
            'hire_position_id' => $position->id,
            'quantity'         => $validated['quantity'],
            'reason'           => $validated['reason'],
            'status'           => 'pending',
        ]);

        return back()->with('success', 'Ticket extension request sent to admins for review.');
    }

    // Admin Debug generator: Creates Dev, Artist, Designer (2 tickets each)
    public function debugSeed(Project $project)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Only admins can use debug tools.');
        }

        $sampleVacancies = [
            [
                'title'       => 'Developer',
                'description' => 'Responsible for core game logic, system architecture, and optimization.',
                'skills'      => ['C++', 'Unreal Engine', 'Git']
            ],
            [
                'title'       => 'Artist',
                'description' => 'Create 3D character models, environmental props, and texture maps.',
                'skills'      => ['Blender', 'Substance Painter', 'ZBrush']
            ],
            [
                'title'       => 'Designer',
                'description' => 'Draft game design documents, design level layouts, and balance core mechanics.',
                'skills'      => ['Level Design', 'System Balance', 'GDD']
            ],
        ];

        foreach ($sampleVacancies as $data) {
            $position = $project->hirePositions()->create([
                'title'          => $data['title'],
                'description'    => $data['description'],
                'tickets_amount' => 2,
                'is_open'        => true,
            ]);

            foreach ($data['skills'] as $skillName) {
                $skill = PositionSkill::firstOrCreate(['title' => $skillName]);
                $position->skills()->attach($skill->id);
            }
        }

        return back()->with('success', 'Debug: Created 3 vacancies (Dev, Artist, Designer) with 2 tickets each!');
    }
}
