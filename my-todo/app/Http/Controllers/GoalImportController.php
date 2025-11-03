<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportGuestGoalsRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GoalImportController extends Controller
{

    /**
     * ゲストのゴールをインポートする
     * 
     * @param ImportGuestGoalsRequest $request
     * @return \Illuninate\Http\JsonResponse
     */
    public function import(ImportGuestGoalsRequest $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'message' => '認証が必要です',
                'code' => 'unauthorized',
            ], 401);
        }

        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validated();

        try {
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

            $goals = $user->goals()->with('tasks')->latest()->get();

            return response()->json($goals, 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => '入力に誤りがあります',
                'code' => 'validation_error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // 内部エラーの詳細はログに残しつつ、クライアントには汎用メッセージのみ返す
            Log::error('Guest goals import failed', [
                'user_id' => $user->id ?? null,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'データのインポートに失敗しました。',
                'code' => 'import_failed'
            ], 500);
        }


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
