<?php

declare(strict_types=1);

namespace App\Domain\Todo\Builders;

use App\Domain\Recurrence\Builders\RecurrenceBuilder;
use App\Domain\Todo\Models\Todo;

/**
 * @template TModel of Todo
 *
 * @extends RecurrenceBuilder<Todo>
 */
class TodoBuilder extends RecurrenceBuilder {}
