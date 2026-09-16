<?php

namespace App\Http\Controllers;

use App\Models\HirePosition;
use App\Models\PositionSkill;
use App\Models\PositionTicket;
use App\Models\Project;
use Illuminate\Http\Request;

class HirePositionController extends Controller
{
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

    // Apply for a position (Create Ticket)
    public function apply(Request $request, HirePosition $position)
    {
        $validated = $request->validate([
            'reason' => 'required|string|min:10',
        ]);

        PositionTicket::create([
            'position_id' => $position->id,
            'user_id'     => auth()->id(),
            'reason'      => $validated['reason'],
            'expired'     => false,
            'success'     => null, // Pending review
        ]);

        return back()->with('success', 'Application ticket submitted.');
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
}
