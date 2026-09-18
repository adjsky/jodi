<?php

declare(strict_types=1);

namespace App\Domain\Todo\Actions;

use App\Domain\Identity\Models\User;
use App\Domain\Todo\Data\Output\CategoryData;
use App\Support\Actions\JodiAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class ListCategories extends JodiAction
{
    /**
     * @return Collection<int, CategoryData>
     */
    public function handle(User $user): Collection
    {
        return CategoryData::collect($user->categories()->get());
    }

    public function asController(): JsonResponse
    {
        $categories = $this->handle($this->user());

        return response()->json($categories);
    }
}
