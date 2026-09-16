<?php

namespace App\Http\Controllers;

use App\Models\HirePosition;
use App\Models\PositionSkill;
use App\Models\PositionTicket;
use App\Models\Project;
use Illuminate\Http\Request;

class HirePositionController extends Controller
{

    // List all vacancies for a project
    public function index(Project $project)
    {
        $positions = $project->hirePositions()
            ->with(['skills', 'tickets'])
            ->where('is_open', true)
            ->get();

        return view('projects.vacancies', compact('project', 'positions'));
    }

    // Create new hiring position for a project
    public function store(Request $request, Project $project)
    {
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

    // Submit an application ticket
    public function apply(Request $request, HirePosition $position)
    {
        $request->validate([
            'reason' => 'required|string|min:10|max:1000',
        ]);

        $alreadyApplied = PositionTicket::where('position_id', $position->id)
            ->where('user_id', auth()->id())
            ->exists();

        if ($alreadyApplied) {
            return back()->with('error', 'You have already applied for this position.');
        }

        PositionTicket::create([
            'position_id' => $position->id,
            'user_id'     => auth()->id(),
            'reason'      => $request->input('reason'),
            'expired'     => false,
            'success'     => null, // Pending review
        ]);

        return back()->with('success', 'Application ticket submitted successfully!');
    }

    // Mark ticket as expired
    public function expireTicket(PositionTicket $ticket)
    {
        $ticket->update(['expired' => true]);

        return back()->with('success', 'Ticket marked as expired.');
    }

    // Accept / Reject application ticket
    public function reviewTicket(Request $request, PositionTicket $ticket)
    {
        $validated = $request->validate([
            'success' => 'required|boolean',
        ]);

        $ticket->update(['success' => $validated['success']]);

        return back()->with('success', 'Ticket status updated.');
    }

    // Admin Debug generator: Creates Dev, Artist, Designer (2 tickets each)
    public function debugSeed(Project $project)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Only admins can use debug tools.');
        }

        $sampleVacancies = [
            [
                'title' => 'Developer',
                'description' => 'Responsible for core game logic, system architecture, and optimization.',
                'skills' => ['C++', 'Unreal Engine', 'Git']
            ],
            [
                'title' => 'Artist',
                'description' => 'Create 3D character models, environmental props, and texture maps.',
                'skills' => ['Blender', 'Substance Painter', 'ZBrush']
            ],
            [
                'title' => 'Designer',
                'description' => 'Draft game design documents, design level layouts, and balance core mechanics.',
                'skills' => ['Level Design', 'System Balance', 'GDD']
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
