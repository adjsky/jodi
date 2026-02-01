<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Todo\CompleteRequest;
use App\Http\Requests\Todo\CreateRequest;
use App\Http\Requests\Todo\DestroyRequest;
use App\Http\Requests\Todo\ReorderRequest;
use App\Http\Requests\Todo\UpdateRequest;
use App\Models\Todo;
use Illuminate\Support\Facades\DB;

class TodoController extends Controller
{
    public function create(CreateRequest $request)
    {
        $data = $request->validatedInSnakeCase();
        $categoryId = isset($data['category'])
            ? $this->user()->categories()
                ->where('name', $data['category'])
                ->firstOrFail(['id'])
                ->id
            : null;

        $this->user()->todos()->create([...$data, 'category_id' => $categoryId]);

        return back();
    }

    public function reorder(ReorderRequest $request)
    {
        $data = $request->validated();

        $user = $this->user();
        $categories = $user->categories()->pluck('id', 'name');

        DB::transaction(function () use ($user, $categories, $data) {
            // TODO: use a CASE/WHEN query if this will seriously slow down the request.
            foreach ($data['todos'] as $todo) {
                $category = $todo['category'] ?? null;

                $user->todos()
                    ->where('id', $todo['id'])
                    ->update([
                        'position' => $todo['position'],
                        'category_id' => $category && isset($categories[$category])
                            ? $categories[$category]
                            : null,
                    ]);
            }
        });

        return back();
    }

    public function update(UpdateRequest $request, Todo $todo)
    {
        $data = $request->validatedInSnakeCase();

        DB::transaction(function () use ($data, $todo) {
            if (array_key_exists('category', $data)) {
                $name = $data['category'];
                $data['category_id'] = $name
                    ? $this->user()->categories()
                        ->where('name', $data['category'])
                        ->firstOrFail(['id'])
                        ->id
                    : null;
                unset($data['category']);
            }

            $todo->fill($data);

            // TODO: don't change position if only time is changed.
            if ($todo->isDirty(['category_id', 'scheduled_at'])) {
                $todo->position = $todo->getHighestOrderNumber() + 1;
            }

            $todo->save();
        });

        return back();
    }

    public function destroy(DestroyRequest $request, Todo $todo)
    {
        $todo->delete();

        return back();
    }

    public function complete(CompleteRequest $request, Todo $todo)
    {
        $todo->completed_at = $todo->completed_at ? null : now();
        $todo->save();

        return back();
    }
}
