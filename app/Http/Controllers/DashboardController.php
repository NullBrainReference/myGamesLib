<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Enums\UserRole;
use App\Models\HirePosition;
use App\Models\HirePosTicketRequest;
use App\Models\ProjectPositionRequest;

class DashboardController extends Controller
{
    public function users(Request $request)
    {
        $query = User::query();

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%");
        }

        $users = $query->orderByDesc('created_at')->paginate(10);

        return view('dashboard.index', compact('users', 'search'));
    }

    public function updateRole(Request $request, int $id)
    {
        $request->validate([
            'role' => ['required', Rule::in(array_column(UserRole::cases(), 'value'))],
        ]);

        $user = User::findOrFail($id);
        $user->role = $request->input('role');
        $user->save();

        return back()->with('success', 'Role updated.');
    }

    public function ticketRequests(Request $request)
    {
        $search = trim($request->input('search'));

        $requests = HirePosTicketRequest::with(['hirePosition.project'])
            ->when($search, function ($query, $search) {
                $query->whereHas('hirePosition', function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhereHas('project', function ($pq) use ($search) {
                          $pq->where('title', 'like', "%{$search}%");
                      });
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('dashboard.ticket-requests', compact('requests'));
    }

    public function approveTicketRequest(Request $request, HirePosTicketRequest $ticketRequest)
    {
        if ($ticketRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        $validated = $request->validate([
            'granted_quantity' => 'required|integer|min:1',
        ]);

        $granted = (int) $validated['granted_quantity'];
        $position = $ticketRequest->hirePosition;

        // Add granted quantity to total ticket capacity pool
        $position->increment('tickets_amount', $granted);

        // Ensure position is open if it was previously closed
        if (!$position->is_open) {
            $position->update(['is_open' => true]);
        }

        // Mark request as approved
        $ticketRequest->update([
            'status'           => 'approved',
            'granted_quantity' => $granted,
        ]);

        return back()->with('success', "Granted +{$granted} extra tickets for position '{$position->title}'. Position is now live for new applicants.");
    }

    public function declineTicketRequest(HirePosTicketRequest $ticketRequest)
    {
        $ticketRequest->update([
            'status' => 'declined',
        ]);

        return back()->with('success', "Ticket request #{$ticketRequest->id} declined.");
    }

    public function positionRequests(Request $request)
    {
        $query = ProjectPositionRequest::with('project');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', "%{$search}%")
                  ->orWhereHas('project', function ($q) use ($search) {
                      $q->where('title', 'like', "%{$search}%");
                  });
        }

        $requests = $query->latest()->paginate(15)->withQueryString();

        return view('dashboard.position-requests', compact('requests'));
    }

    /**
     * Approve a position slot request and automatically spawn the new HirePosition.
     */
    public function approvePositionRequest(ProjectPositionRequest $positionRequest)
    {
        if ($positionRequest->status !== 'pending') {
            return back()->with('error', 'Request has already been processed.');
        }

        $positionRequest->update(['status' => 'approved']);

        // Auto-create the position for the project
        HirePosition::create([
            'project_id'     => $positionRequest->project_id,
            'title'          => $positionRequest->title,
            'description'    => "Approved by Admin: " . $positionRequest->reason,
            'tickets_amount' => $positionRequest->quantity,
            'is_open'        => true,
        ]);

        return back()->with('success', "Approved request! Created vacancy position '{$positionRequest->title}' with {$positionRequest->quantity} ticket slot(s).");
    }

    /**
     * Decline a position slot request.
     */
    public function declinePositionRequest(ProjectPositionRequest $positionRequest)
    {
        if ($positionRequest->status !== 'pending') {
            return back()->with('error', 'Request has already been processed.');
        }

        $positionRequest->update(['status' => 'rejected']);

        return back()->with('success', "Position slot request for '{$positionRequest->title}' has been declined.");
    }
}
