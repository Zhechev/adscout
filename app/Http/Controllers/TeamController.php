<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Http\Requests\StoreTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Http\Resources\TeamResource;
use Illuminate\Http\JsonResponse;
use App\Constants\MessageConstants;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $teams = Team::paginate();

        return response()->json([
            'success' => true,
            'data' => TeamResource::collection($teams),
            'total' => $teams->total(),
            'current_page' => $teams->currentPage(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTeamRequest $request
     * @return JsonResponse
     */
    public function store(StoreTeamRequest $request): JsonResponse
    {
        $team = Team::create($request->validated());

        return response()->json([
            'success' => true,
            'data' => new TeamResource($team),
            'message' => MessageConstants::TEAM_CREATED,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Team $team
     * @return JsonResponse
     */
    public function show(Team $team): JsonResponse
    {
        return response()->json(['success' => true, 'data' => new TeamResource($team)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateTeamRequest $request
     * @param Team $team
     * @return JsonResponse
     */
    public function update(UpdateTeamRequest $request, Team $team): JsonResponse
    {
        $team->update($request->validated());

        return response()->json([
            'success' => true,
            'data' => new TeamResource($team),
            'message' => MessageConstants::TEAM_UPDATED
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Team $team
     * @return JsonResponse
     */
    public function destroy(Team $team): JsonResponse
    {
        $team->delete();

        return response()->json(['success' => true, 'message' => MessageConstants::TEAM_DELETED]);
    }
}
