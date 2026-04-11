<?php

declare(strict_types=1);

namespace App\Domain\Todo\Models;

use Database\Factories\TodoPositionFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[UseFactory(TodoPositionFactory::class)]
class TodoPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'occurs_at',
        'position',
    ];

    protected $hidden = [];

    protected function casts(): array
    {
        return [
            'occurs_at' => 'date:Y-m-d',
        ];
    }
}
