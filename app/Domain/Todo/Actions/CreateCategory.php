<?php

declare(strict_types=1);

namespace App\Domain\Todo\Actions;

use App\Domain\Identity\Models\User;
use App\Domain\Todo\Data\Input\CreateCategoryData;
use App\Domain\Todo\Models\Category;
use App\Support\Actions\JodiAction;
use App\Support\Http\JodiRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class CreateCategory extends JodiAction
{
    public function handle(User $user, CreateCategoryData $data): Category
    {
        $category = $user->categories()->create(['name' => $data->name]);

        return $category;
    }

    public function asController(JodiRequest $request): RedirectResponse
    {
        $category = $this->handle($this->user(), CreateCategoryData::from($request));

        Inertia::flash('category', $category);

        return back();
    }
}
