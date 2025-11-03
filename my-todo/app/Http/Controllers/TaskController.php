<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\Task;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
            //task作成時のデータ情報が正しいか確認する
            $validated = $request->validate([
                'goal_id' => 'required|exists:goals,id',
                'task' => 'required|string|max:255',
            ]);
    
            $task = Task::create([
                'goal_id' => $validated['goal_id'],
                'task' => $validated['task'],
            ]);
    
            // データベースから再取得してstatusを含める
            $taskWithStatus = Task::find($task->id);
            return response()->json($taskWithStatus, 201);
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

    public function updateStatus(Request $request, Goal $goal, Task $task)
    {
        try {
            $this->authorize('update', $task);
    
            // taskがgoalに属しているかチェック
            if ($task->goal_id !== $goal->id) {
                return response()->json([
                    'error' => 'このTaskは指定されたGoalに属していません。',
                ], 403);
            }
    
            $request->validate([
                'status' => 'required|in:todo,doing,done',
            ]);
    
            //リクエストのステータスをタスクに保存
            $task->status = $request->status;
            $task->save();
    
            return response()->json([
                'status' => $task->status,
                'message' => 'ステータスが更新されました。',
            ], 200);
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
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    // I didn't think it was necessary, so I didn't implement it.
    // public function update(Request $request, Task $task)
    // {
    //     $this->authorize('update', $task);

    //     $validated  = $request->validate([
    //         'task' => 'required|string|max:255',
    //     ]);
    //     $task->update($validated);
    // }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Goal $goal, Task $task)
    {
        try {
            $this->authorize('delete', $task);
    
            // Goalに属するTaskであるか確認（念のため）
            if ($task->goal_id !== $goal->id) {
                return response()->json([
                    'error' => 'このTaskは指定されたGoalに属していません。',
                ], 403);
            }
    
            $task->delete();
    
            return response()->json([
                'success' => true,
                'message' => 'Taskの削除に成功しました。',
            ], 200);
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
}
