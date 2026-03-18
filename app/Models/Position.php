<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\PositionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    /** @use HasFactory<PositionFactory> */
    use HasFactory;

    protected $fillable = [
        'date',
        'position',
    ];

    protected $hidden = [];

    /**
     * @return array{date: "date:Y-m-d"}
     */
    protected function casts(): array
    {
        return [
            'date' => 'date:Y-m-d',
        ];
    }
}
