<?php

declare(strict_types=1);

namespace App\Domain\Todo\Actions;

use App\Domain\Todo\Models\Category;
use App\Support\Actions\Action;
use App\Support\Http\JodiRequest;
use Illuminate\Http\RedirectResponse;

class DestroyCategory extends Action
{
    public function handle(Category $category): void
    {
        $category->delete();
    }

    public function authorize(JodiRequest $request): bool
    {
        return $this->user()->can('destroy', $request->category);
    }

    public function asController(Category $category): RedirectResponse
    {
        $this->handle($category);

        return back();
    }
}
