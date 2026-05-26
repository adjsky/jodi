<?php

declare(strict_types=1);

namespace App\Http\Controllers;

class CalendarController extends Controller
{
    public function __invoke()
    {
        return inertia('Calendar');
    }
}
