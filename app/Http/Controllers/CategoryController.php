<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Category\DestroyRequest;

class CategoryController extends Controller
{
    public function destroy(DestroyRequest $request, string $name)
    {
        $request->input('category')->delete();

        return back();
    }
}
