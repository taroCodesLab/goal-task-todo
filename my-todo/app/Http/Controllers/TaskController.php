<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

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

        }catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request, Task $task)
    {
        $request->validate([
            'status' => 'required|in:未着手,進行中,完了',
        ]);

        //リクエストのステータスをタスクに保存
        $task->status = $request->status;
        $task->save();

        return response()->json([
            'status' => $task->status,
            'message' => 'ステータスが更新されました。',
        ]);
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
    public function update(Request $request, Task $task)
    {
        $validated  = $request->validate([
            'task' => 'required|string|max:255',
        ]);
        $task->update($validated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        try {
            $task->delete();

            return response()->json([
                'success' => true,
                'message' => 'Taskの削除に成功しました。',
            ], 200);
        }catch (\Exception $e){
            return response()->json([
                'error' => $e->getMessage(),
            ]);
        }
    }
}
