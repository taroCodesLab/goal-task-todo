<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportGuestGoalsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GoalImportController extends Controller
{
    public function import(ImportGuestGoalsRequest $request)
    {
        if (!Auth::check()) {
            abort(401, '認証されていません');
        }

        $user = Auth::user();

        $validated = $request->validated();

        DB::transaction(function () use ($validated, $user) {
            foreach ($validated['goals'] as $goalData) {
                $goal = $user->goals()->firstOrCreate([
                    'goal' => $goalData['goal'],
                ], [
                    'created_at' => $goalData['createdAt'],
                    'updated_at' => now(),
                ]);

                foreach ($goalData['tasks'] ?? [] as $taskData) {
                    $goal->tasks()->firstOrCreate([
                        'task' => $taskData['task'],
                    ], [
                        'status' => $taskData['status'] ?? '未着手',
                        'created_at' => $taskData['createdAt'] ?? now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        });

        return response()->json($user->goals()->with('tasks')->latest()->get());
        // foreach ($validated->input('goals', []) as $goalData) {
        //     $goal = $user->goals()->create([
        //         'goal' => $goalData['goal'],
        //         'created_at' => $goalData['createdAt'],
        //         'updated_at' => now(),
        //     ]);

        //     foreach ($goalData['tasks'] ?? [] as $taskData) {
        //         $goal->tasks()->create([
        //             'task' => $taskData['task'],
        //             'status' => $taskData['status'] ?? '未着手',
        //             'created_at' => $taskData['createdAt'] ?? now(),
        //             'updated_at' => now(),
        //         ]);
        //     }
        // }

        
    }
}
