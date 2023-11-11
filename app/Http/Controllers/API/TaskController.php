<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->validate($request, [
            'sortBy' => 'in:completed_at,created_at',
            'sortByDesc' => 'in:completed_at,created_at'
        ]);
        $query = Task::query();

        if ($request->sortBy || $request->sortByDesc) {
            $direction = $request->sortByDesc ? 'DESC' : 'ASC';
            $sortBy = $request->sortBy ?? $request->sortByDesc;
            $query->orderBy($sortBy, $direction);
        }

        if ($request->text) {
            $query->where(fn($q) => $q->where('name', 'LIKE', '%' . $request->text . '%')
                ->orWhere('description', 'LIKE', '%' . $request->text . '%'));
        }

        return TaskResource::collection($query->paginate(20));
    }

    public function show(Task $task): TaskResource
    {
        return new TaskResource($task);
    }

    public function store(TaskRequest $request): TaskResource
    {
        $task = Task::create($request->validated());

        return new TaskResource($task);
    }

    public function update(Task $task, TaskRequest $request): TaskResource
    {
        $task->update($request->validated());

        return new TaskResource($task);
    }

    public function complete(Task $task): TaskResource
    {
        $task->update(['completed_at' => now()]);

        return new TaskResource($task);
    }

    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return response()->json(status: 204);
    }
}
