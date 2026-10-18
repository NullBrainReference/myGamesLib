<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiProjectController extends Controller
{
    /**
     * Get all projects where the authenticated user is involved.
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $projects = Project::whereHas('owners', function ($query) use ($userId) {
                $query->where('users.id', $userId);
            })
            ->orWhereHas('editors', function ($query) use ($userId) {
                $query->where('users.id', $userId);
            })
            ->orWhereHas('participants', function ($query) use ($userId) {
                $query->where('users.id', $userId);
            })
            ->orWhere('is_public', true)
            ->with(['owners:id,name', 'editors:id,name', 'participants:id,name'])
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $projects->items(),
            'current_page' => $projects->currentPage(),
            'last_page' => $projects->lastPage(),
            'total' => $projects->total(),
        ]);
    }

    /**
     * View a single project with details.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $project = Project::with(['owners', 'editors', 'participants'])->find($id);

        if (!$project) {
            return response()->json(['message' => 'Project not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'project' => $project
        ]);
    }
}