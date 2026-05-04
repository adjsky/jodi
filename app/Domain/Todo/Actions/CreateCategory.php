<?php

declare(strict_types=1);

namespace App\Domain\Todo\Actions;

use App\Domain\Identity\Models\User;
use App\Domain\Todo\Data\Input\CreateCategoryData;
use App\Support\Actions\JodiAction;
use App\Support\Http\JodiRequest;
use Illuminate\Http\RedirectResponse;

class CreateCategory extends JodiAction
{
    public function handle(User $user, CreateCategoryData $data): int
    {
        $category = $user->categories()->create(['name' => $data->name]);

        return $category->id;
    }

    public function asController(JodiRequest $request): RedirectResponse
    {
        $id = $this->handle($this->user(), CreateCategoryData::from($request));

        $request->setFlash('id', $id);

        return back();
    }
}
