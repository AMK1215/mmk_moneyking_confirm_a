<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResultDeleteController extends Controller
{
    public function deleteResultsByPlayerId(Request $request)
    {
        // Validate the input
        $validated = $request->validate([
            'player_id' => 'required|string|max:50',
        ]);

        $playerId = $validated['player_id'];

        // Perform the deletion
        $deleted = DB::table('results')->where('player_id', $playerId)->delete();

        if ($deleted) {
            return response()->json(['message' => 'Results deleted successfully.'], 200);
        }

        return response()->json(['message' => 'No results found for the provided player_id.'], 404);
    }

    public function deleteResults(Request $request)
    {
        // Validate the input
        $validated = $request->validate([
            'player_id' => 'required|string|max:50',
            'game_provide_name' => 'required|string|max:100',
        ]);

        $playerId = $validated['player_id'];
        $gameProvideName = $validated['game_provide_name'];

        // Perform the deletion
        $deleted = DB::table('results')
            ->where('player_id', $playerId)
            ->where('game_provide_name', $gameProvideName)
            ->delete();

        if ($deleted) {
            return response()->json(['message' => 'Results deleted successfully.'], 200);
        }

        return response()->json(['message' => 'No results found for the provided criteria.'], 404);
    }
}
