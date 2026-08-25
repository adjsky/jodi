<?php

declare(strict_types=1);

namespace App\Domain\Todo\Models;

use App\Domain\Identity\Models\User;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['name'])]
#[UseFactory(CategoryFactory::class)]
class Category extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
