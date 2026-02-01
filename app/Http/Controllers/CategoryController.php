<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Category\CreateRequest;
use App\Http\Requests\Category\DestroyRequest;

class CategoryController extends Controller
{
    public function create(CreateRequest $request)
    {
        $data = $request->validated();

        $this->user()->categories()->create(['name' => $data['name']]);

        return back();
    }

    public function destroy(DestroyRequest $request)
    {
        $request->input('category')->delete();

        return back();
    }
}
