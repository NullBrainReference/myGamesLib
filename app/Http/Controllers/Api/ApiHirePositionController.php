<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HirePosTicketRequest;
use App\Models\HirePosition;
use App\Models\PositionSkill;
use App\Models\PositionTicket;
use App\Models\Project;
use App\Models\ProjectPositionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiHirePositionController extends Controller
{
    /**
     * Get all open vacancies for a project.
     */
    public function index(Project $project): JsonResponse
    {
        $positions = $project->hirePositions()
            ->with(['skills', 'tickets'])
            ->where('is_open', true)
            ->latest()
            ->get();

        return response()->json([
            'success'   => true,
            'positions' => $positions
        ]);
    }

    /**
     * Create a new hire position directly (Owners/Admins only).
     */
    public function store(Request $request, Project $project): JsonResponse
    {
        $user = $request->user();

        if (!$user->isAdmin() && !$project->owners->contains($user->id)) {
            return response()->json(['message' => 'Unauthorized.'], 403);
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

        if (!empty($validated['skills'])) {
            $skillIds = collect($validated['skills'])->map(function ($skillName) {
                return PositionSkill::firstOrCreate(['title' => trim($skillName)])->id;
            });
            $position->skills()->sync($skillIds);
        }

        return response()->json([
            'success'  => true,
            'message'  => 'Hire position published successfully.',
            'position' => $position->load('skills')
        ], 201);
    }

    /**
     * Submit an application ticket for a position.
     */
    public function apply(Request $request, HirePosition $position): JsonResponse
    {
        $user = $request->user();

        if (!$position->hasAvailableTickets()) {
            return response()->json([
                'message' => 'No application tickets remaining for this position.'
            ], 422);
        }

        $alreadyApplied = PositionTicket::where('position_id', $position->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyApplied) {
            return response()->json([
                'message' => 'You have already applied for this position.'
            ], 422);
        }

        $validated = $request->validate([
            'reason' => 'required|string|min:10|max:1000',
        ]);

        $ticket = PositionTicket::create([
            'position_id' => $position->id,
            'user_id'     => $user->id,
            'reason'      => $validated['reason'],
            'expired'     => false,
            'success'     => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application ticket submitted successfully!',
            'ticket'  => $ticket
        ], 201);
    }

    /**
     * List all applicant tickets across positions for a project (Owners/Admins only).
     */
    public function applicants(Request $request, Project $project): JsonResponse
    {
        $user = $request->user();

        if (!$user->isAdmin() && !$project->owners->contains($user->id)) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $query = $project->positionTickets()
            ->with(['position', 'user:id,name,email']);

        if ($request->filled('position_id')) {
            $query->where('position_id', $request->input('position_id'));
        }

        $tickets = $query->latest()->paginate(15);

        return response()->json([
            'success'      => true,
            'tickets'      => $tickets->items(),
            'current_page' => $tickets->currentPage(),
            'last_page'    => $tickets->lastPage(),
        ]);
    }

    /**
     * Accept or decline an applicant ticket (Owners/Admins only).
     */
    public function reviewTicket(Request $request, PositionTicket $ticket): JsonResponse
    {
        $user = $request->user();
        $project = $ticket->position->project;

        if (!$user->isAdmin() && !$project->owners->contains($user->id)) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'action' => 'required|in:accept,decline',
            'reason' => 'required|string|min:5|max:1000',
        ]);

        $isAccepted = $validated['action'] === 'accept';

        $ticket->update([
            'success'     => $isAccepted,
            'explanation' => $validated['reason'],
        ]);

        if ($isAccepted) {
            $project->participants()->syncWithoutDetaching([$ticket->user_id]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ticket reviewed successfully.',
            'ticket'  => $ticket
        ]);
    }

    /**
     * Request additional TICKET CAPACITY for an existing position from admins.
     */
    public function requestMoreTickets(Request $request, Project $project): JsonResponse
    {
        $user = $request->user();

        if (!$user->isAdmin() && !$project->owners->contains($user->id)) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'hire_position_id' => 'required|exists:hire_positions,id',
            'quantity'         => 'required|integer|min:1|max:50',
            'reason'           => 'required|string|min:10|max:1000',
        ]);

        $position = $project->hirePositions()->findOrFail($validated['hire_position_id']);

        $ticketRequest = HirePosTicketRequest::create([
            'hire_position_id' => $position->id,
            'quantity'         => $validated['quantity'],
            'reason'           => $validated['reason'],
            'status'           => 'pending',
        ]);

        return response()->json([
            'success'        => true,
            'message'        => 'Ticket extension request submitted to admins.',
            'ticket_request' => $ticketRequest
        ], 201);
    }

    /**
     * Request NEW POSITION SLOTS (e.g. Developer, Artist) from admins.
     */
    public function requestPositionSlots(Request $request, Project $project): JsonResponse
    {
        $user = $request->user();

        if (!$user->isAdmin() && !$project->owners->contains($user->id)) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'title'    => 'required|string|max:255',
            'quantity' => 'required|integer|min:1|max:20',
            'reason'   => 'required|string|min:10|max:1000',
        ]);

        $positionRequest = ProjectPositionRequest::create([
            'project_id' => $project->id,
            'title'      => $validated['title'],
            'quantity'   => $validated['quantity'],
            'reason'     => $validated['reason'],
            'status'     => 'pending',
        ]);

        return response()->json([
            'success'          => true,
            'message'          => 'New position slot request submitted to admins.',
            'position_request' => $positionRequest
        ], 201);
    }

    /**
     * Admin Review: Approve or Reject a position slot request.
     * When approved, creates the requested HirePosition automatically.
     */
    public function reviewPositionSlotRequest(Request $request, ProjectPositionRequest $positionRequest): JsonResponse
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Only admins can review requests.'], 403);
        }

        $validated = $request->validate([
            'action'      => 'required|in:approve,reject',
            'description' => 'nullable|string',
        ]);

        if ($validated['action'] === 'approve') {
            $positionRequest->update(['status' => 'approved']);

            // Auto-create the position with requested params
            $newPosition = $positionRequest->project->hirePositions()->create([
                'title'          => $positionRequest->title,
                'description'    => $validated['description'] ?? "Position approved by Admin: {$positionRequest->reason}",
                'tickets_amount' => $positionRequest->quantity,
                'is_open'        => true,
            ]);

            return response()->json([
                'success'  => true,
                'message'  => 'Position request approved and vacancy created.',
                'position' => $newPosition
            ]);
        }

        $positionRequest->update(['status' => 'rejected']);

        return response()->json([
            'success' => true,
            'message' => 'Position request rejected.'
        ]);
    }
}