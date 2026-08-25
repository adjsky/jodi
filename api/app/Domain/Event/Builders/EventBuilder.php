<?php

declare(strict_types=1);

namespace App\Domain\Event\Builders;

use App\Domain\Event\Models\Event;
use App\Domain\Recurrence\Builders\RecurrenceBuilder;

/**
 * @template TModel of Event
 *
 * @extends RecurrenceBuilder<TModel>
 */
class EventBuilder extends RecurrenceBuilder {}
