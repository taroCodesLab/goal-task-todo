<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GoalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
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
        return view('goals.index', ['goals' => $goals]);
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
        $this->authorize('update', $goal);

        $validated = $request->validate(['goal' => 'required|string|max:255']);
        $goal->update($validated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Goal $goal)
    {
        $this->authorize('delete', $goal);

        try {
            
            $goal->delete();

            return response()->noContent();
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateOrder(Request $request)
    {
        $orderData = $request->input('order');

        DB::transaction(function () use ($orderData) {
            foreach ($orderData as $index => $item) {
                Goal::where('id', $item['id'])->update(['order' => $index + 1]);
            }
        });

        return response()->json(['success' => true]);
    }
}