<?php

declare(strict_types=1);

namespace App\Domain\Todo\Data\Output;

use App\Domain\Todo\Models\Category;
use App\Support\Data\JodiData;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CategoryData extends JodiData
{
    public function __construct(public int $id, public string $name) {}

    public static function fromModel(Category $category): self
    {
        return new self($category->id, $category->name);
    }
}
