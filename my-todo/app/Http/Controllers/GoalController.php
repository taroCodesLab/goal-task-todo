<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GoalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            //GoalとGoal下のTaskのデータを作成順で$goalsに格納
            //コードの可読性を優先し、fnの使用を避けた
            $goals = Goal::where('user_id', auth()->id())
                ->withTasksOrdered()
                ->get()
                ->map(function ($goal) {
                    return [
                        'id' => $goal->id,
                        'goal' => $goal->goal,
                        'completion_rate' => $goal->completeRate(),
                        //Goal下にあるTaskの必要なデータの取得
                        'tasks' => $goal->tasks->map(fn ($task) => [
                            'id' => $task->id,
                            'task' => $task->task,
                            'status' => $task->status,
                        ])
                    ];
                });
            
            // For API
            if ($request->wantsJson()) {
                return response()->json($goals, 200);
            }  

            // For Browser
            return view('goals.index', ['goals' => $goals]);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'message' => 'サーバーでエラーが発生しました',
                'code' => 'server_error',
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'goal' => 'required|string|max:255',
            ]);
    
            $validated['user_id'] = auth()->id();
            $goal = Goal::create($validated);
            
            return response()->json([
                'id' => $goal->id,
                'goal' => $goal->goal,
                'completion_rate' => $goal->completeRate(),
                'tasks' => [],
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => '入力に誤りがあります',
                'code' => 'validation_error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'message' => 'サーバーでエラーが発生しました',
                'code' => 'server_error',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Goal $goal)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Goal $goal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Goal $goal)
    {
        try {
            $this->authorize('update', $goal);
    
            $validated = $request->validate(['goal' => 'required|string|max:255']);
            $goal->update($validated);

            return response()->json([
                'id' => $goal->id,
                'goal' => $goal->goal,
                'completion_rate' => $goal->completeRate(),
                'tasks' => $goal->tasks->map(fn ($task) => [
                    'id' => $task->id,
                    'task' => $task->task,
                    'status' => $task->status,
                ]),
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => '入力に誤りがあります',
                'code' => 'validation_error',
                'errors' => $e->errors(),
            ], 422);
        } catch (AuthorizationException $e) {
            return response()->json([
                'message' => '操作が認可されていません',
                'code' => 'forbidden',
            ], 403);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'message' => 'サーバーでエラーが発生しました',
                'code' => 'server_error',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Goal $goal)
    {
        try {
            $this->authorize('delete', $goal);
    
            $goal->delete();
    
            return response()->noContent();
        } catch (AuthorizationException $e) {
            return response()->json([
                'message' => '操作が許可されていません',
                'code' => 'forbidden',
            ], 403);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'message' => 'サーバーでエラーが発生しました',
                'code' => 'server_error',
            ], 500);
        }
        
    }

    public function updateOrder(Request $request)
    {
        try {
            $orderData = $request->input('order');
    
            DB::transaction(function () use ($orderData) {
                foreach ($orderData as $index => $item) {
                    Goal::where('id', $item['id'])->update(['order' => $index + 1]);
                }
            });
    
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'message' => 'サーバーでエラーが発生しました',
                'code' => 'server_error',
            ], 500);
        }
    }
}