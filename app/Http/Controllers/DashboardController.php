<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Enums\UserRole;
use App\Models\Comment;
use App\Models\HirePosition;
use App\Models\HirePosTicketRequest;
use App\Models\ProjectPositionRequest;
use App\Models\ProjectApproveRequest;

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

    /**
     * Display listing of pending & resolved project approval requests.
     */
    public function projectRequests(Request $request)
    {
        $query = ProjectApproveRequest::with(['project', 'user', 'comment.upvotes']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('project', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            })->orWhereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $requests = $query->latest()->paginate(15);

        return view('dashboard.project-requests', compact('requests'));
    }

    /**
     * Approve a Project Approval Request.
     */
    public function approveProjectRequest(int $id)
    {
        $approveRequest = ProjectApproveRequest::with(['project.owners', 'project.mechanics'])->findOrFail($id);
        $project = $approveRequest->project;

        DB::transaction(function () use ($approveRequest, $project) {
            // 1. Compile participants from project owners, origin comment author, and thread replies
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

            // 2. Compile editors from users who authored mechanics
            $editorIds = $project->mechanics->pluck('user_id')->filter()->unique()->toArray();
            $project->editors()->sync($editorIds);

            // 3. Mark project as approved
            if (Schema::hasColumn('projects', 'is_approved')) {
                $project->update(['is_approved' => true]);
            }

            // 4. Update request entity status
            $approveRequest->update(['status' => 'approved']);
        });

        return back()->with('success', 'Project approval request approved! Participants and Editors synced automatically.');
    }

    /**
     * Reject/Decline a Project Approval Request.
     */
    public function rejectProjectRequest(int $id)
    {
        $approveRequest = ProjectApproveRequest::findOrFail($id);

        if ($approveRequest->status !== 'pending') {
            return back()->with('error', 'This approval request has already been resolved.');
        }

        $approveRequest->update(['status' => 'rejected']);

        return back()->with('success', 'Project approval request has been rejected. Community members can post a new request later if needed.');
    }

    /**
     * Batch process pending project approval requests based on community voting rules:
     * - Minimum 3 total votes
     * - At least 75% positive upvotes
     * - Created > 12 hours ago (commented line)
     */
    public function processBatchApprovals()
    {
        $pendingRequests = ProjectApproveRequest::where('status', 'pending')
            // ->where('created_at', '<=', now()->subHours(12)) // Uncomment to enforce 12-hour age rule
            ->with(['project.owners', 'project.mechanics', 'comment.votes'])
            ->get();

        $approvedCount = 0;

        foreach ($pendingRequests as $approveRequest) {
            $comment = $approveRequest->comment;
            if (!$comment) {
                continue;
            }

            // 1. Calculate voting metrics
            $totalVotes = $comment->votes->count();
            $upvotes = $comment->votes->where('vote', '>', 0)->count();

            // 2. Evaluate voting criteria: total votes >= 3 & upvote percentage >= 75%
            if ($totalVotes >= 3) {
                $upvoteRate = ($upvotes / $totalVotes) * 100;

                if ($upvoteRate >= 75) {
                    DB::transaction(function () use ($approveRequest) {
                        $project = $approveRequest->project;

                        if ($project) {
                            // Sync team participants (owners + thread author + reply authors)
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

                            // Sync editors (authors of proposed mechanics)
                            $editorIds = $project->mechanics->pluck('user_id')->filter()->unique()->toArray();
                            $project->editors()->sync($editorIds);

                            // Mark project as approved
                            if (Schema::hasColumn('projects', 'is_approved')) {
                                $project->update(['is_approved' => true]);
                            }
                        }

                        // Mark request as approved
                        $approveRequest->update(['status' => 'approved']);
                    });

                    $approvedCount++;
                }
            }
        }

        return back()->with('success', "Batch evaluation finished! Approved {$approvedCount} request(s) meeting community voting criteria.");
    }
}
