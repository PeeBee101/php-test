<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Str;

class TaskController extends Controller
{
    public function index()
    {
        return response()->json(Task::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:100',
            'description' => 'required|string|min:10|max:5000',
        ]);

        $validated['secure_id'] = (string) Str::uuid();

        $task = Task::create($validated);

        return response()->json($task, 201);
    }

    public function update(Request $request, $secure_id)
    {
        $task = Task::where('secure_id', $secure_id)->first();

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        $task->update($request->only(['name', 'description']));

        return response()->json($task);
    }

    public function destroy($secure_id)
    {
        $task = Task::where('secure_id', $secure_id)->first();

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted']);
    }

    public function restore($secure_id)
    {
        $task = Task::withTrashed()->where('secure_id', $secure_id)->first();

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        $task->restore();

        return response()->json(['message' => 'Task restored']);
    }
}