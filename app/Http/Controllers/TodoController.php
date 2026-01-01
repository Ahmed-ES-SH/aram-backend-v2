<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $todos = Todo::where('user_id', $request->user()->id)
            ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $todos,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'nullable|in:low,medium,high',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $todo = Todo::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'description' => $request->description,
            // Default to medium if not provided
            'priority' => $request->priority ?? 'medium',
            'is_completed' => false,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Todo created successfully',
            'data' => $todo,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $todo = Todo::where('user_id', $request->user()->id)->find($id);

        if (!$todo) {
            return response()->json([
                'status' => false,
                'message' => 'Todo not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $todo,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $todo = Todo::where('user_id', $request->user()->id)->find($id);

        if (!$todo) {
            return response()->json([
                'status' => false,
                'message' => 'Todo not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'is_completed' => 'boolean',
            'priority' => 'nullable|in:low,medium,high',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $todo->update($request->only(['title', 'description', 'is_completed', 'priority']));

        return response()->json([
            'status' => true,
            'message' => 'Todo updated successfully',
            'data' => $todo,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $todo = Todo::where('user_id', $request->user()->id)->find($id);

        if (!$todo) {
            return response()->json([
                'status' => false,
                'message' => 'Todo not found',
            ], 404);
        }

        $todo->delete();

        return response()->json([
            'status' => true,
            'message' => 'Todo deleted successfully',
        ]);
    }
}
