<?php

declare(strict_types=1);

namespace App\Domain\Todo\Actions;

use App\Domain\Todo\Data\Input\CreateCategoryData;
use App\Support\Actions\Action;
use App\Support\Http\JodiRequest;
use Illuminate\Http\RedirectResponse;

class CreateCategory extends Action
{
    public function handle(CreateCategoryData $data): int
    {
        $category = $this->user()->categories()->create(['name' => $data->name]);

        return $category->id;
    }

    public function asController(JodiRequest $request): RedirectResponse
    {
        $id = $this->handle(CreateCategoryData::from($request));

        $request->setFlash('id', $id);

        return back();
    }
}
