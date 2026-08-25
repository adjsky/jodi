<?php

declare(strict_types=1);

namespace App\Domain\Todo\Actions;

use App\Domain\Todo\Models\Category;
use App\Support\Actions\JodiAction;
use App\Support\Http\JodiRequest;
use Illuminate\Http\Response;

class DestroyCategory extends JodiAction
{
    public function handle(Category $category): void
    {
        $category->delete();
    }

    public function authorize(JodiRequest $request): bool
    {
        return $this->user()->can('destroy', $request->category);
    }

    public function asController(Category $category): Response
    {
        $this->handle($category);

        return response()->noContent();
    }
}
