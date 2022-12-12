<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTeamRequest;
use App\Http\Requests\UpdateTeamRequest;

class TeamController extends Controller
{
    public function fetch(Request $request): JsonResponse
    {
        // Get input request
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        // Get team
        $teamQuery = Team::query();

        // Get single data
        if ($id) {
            $team = $teamQuery->find($id);
            // Check response
            if ($team) {
                return ResponseFormatter::success($team, 'Team found');
            }
            return ResponseFormatter::error('Team not found', 404);
        }

        // Get multiple data
        $teams = $teamQuery->where('company_id', $request->input('company_id'));
        if ($name) {
            $teams->where('name', 'like', '%' . $name . '%');
        }

        // Response success
        return ResponseFormatter::success(
            $teams->paginate($limit),
            'Teams found'
        );
    }

    public function create(CreateTeamRequest $request): JsonResponse
    {
        try {
            // Get team
            $teamQuery = Team::query();

            // Upload icon
            if ($request->hasFile('icon')) {
                $path = $request->file('icon')->store('public/icons');
            }

            // Create team
            $team = $teamQuery->create([
                'name' => $request->input('name'),
                'icon' => $path ?? $request->input('icon'),
                'company_id' => $request->input('company_id'),
            ]);

            // Check if company exists
            if (!$team) {
                throw new Exception('Team not created');
            }
            // Response success
            return ResponseFormatter::success($team, 'Team created');
        } catch (Exception $exception) {
            // Response error
            return ResponseFormatter::error($exception->getMessage(), 500);
        }
    }

    public function update(UpdateTeamRequest $request, $id): JsonResponse
    {
        $teamQuery = Team::query();
        try {
            // Get team
            $team = $teamQuery->find($id);

            // Check if team exists
            if (!$team) {
                throw new Exception('Team not found');
            }

            // Upload icon
            if ($request->hasFile('icon')) {
                $path = $request->file('icon')->store('public/icons');
            }

            // Update team
            $team->update([
                'name' => $request->input('name'),
                'icon' => $path ?? $team->value('icon'),
                'company_id' => $request->input('company_id'),
            ]);

            // Response success
            return ResponseFormatter::success($team, 'Team updated');
        } catch (Exception $exception) {
            // Response error
            return ResponseFormatter::error($exception->getMessage(), 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            // Get team
            $team = Team::query()->find($id);

            // Check if team exists
            if (!$team) {
                throw new Exception('Team not found');
            }

            // Delete team
            $team->delete();

            // Response success
            return ResponseFormatter::success(null,'Team deleted');
        } catch (Exception $exception) {
            // Response error
            return ResponseFormatter::error($exception->getMessage(), 500);
        }
    }
}
