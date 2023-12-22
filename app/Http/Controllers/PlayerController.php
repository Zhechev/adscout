<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;
use App\Http\Requests\StorePlayerRequest;
use App\Http\Requests\UpdatePlayerRequest;
use App\Http\Resources\PlayerResource;
use App\Queries\PlayerQuery;
use App\Constants\MessageConstants;

class PlayerController extends Controller
{
    /**
     * Fetches and returns a list of players, allowing filtering and pagination.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request,): \Illuminate\Http\JsonResponse
    {
        // Initialize a query for filtering and pagination
        $query = new PlayerQuery(Player::query(), $request);

        // Filter and paginate players
        $players = $query->filter()->paginate($request->input('per_page', 10));

        // Return the result as JSON
        return response()->json([
            'success' => true,
            'data' => PlayerResource::collection($players),
            'total' => $players->total(),
            'current_page' => $players->currentPage(),
        ]);
    }

    /**
     * Creates a new player and returns a JSON response with the player's data.
     *
     * @param StorePlayerRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StorePlayerRequest $request)
    {
        // Create a player with the validated data from the request
        $player = Player::create($request->validated());

        // Return a JSON response with the created player
        return response()->json([
            'success' => true,
            'data' => new PlayerResource($player),
            'message' => MessageConstants::PLAYER_CREATED
        ], 201);
    }

    /**
     * Returns a JSON response with the data of a specific player.
     *
     * @param Player $player
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Player $player): \Illuminate\Http\JsonResponse
    {
        // Return a JSON response with the player's data
        return response()->json(['success' => true, 'data' => new PlayerResource($player)]);
    }

    /**
     * Updates the data of a specific player and returns a JSON response with the updated data.
     *
     * @param UpdatePlayerRequest $request
     * @param Player $player
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdatePlayerRequest $request, Player $player): \Illuminate\Http\JsonResponse
    {
        // Update the player's data with the validated data from the request
        $player->update($request->validated());

        // Return a JSON response with the updated player's data
        return response()->json([
            'success' => true,
            'data' => new PlayerResource($player),
            'message' => MessageConstants::PLAYER_UPDATED
        ]);
    }

    /**
     * Deletes a specific player and returns a JSON response for successful deletion.
     *
     * @param Player $player
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Player $player): \Illuminate\Http\JsonResponse
    {
        // Delete the player
        $player->delete();

        // Return a JSON response for successful player deletion
        return response()->json(['success' => true, 'message' => MessageConstants::PLAYER_DELETED]);
    }
}
