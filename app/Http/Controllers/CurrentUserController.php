<?php

declare(strict_types=1);

namespace App\Http\Controllers;

class CurrentUserController extends Controller
{
    public function show()
    {
        return inertia('CurrentUser');
    }
}
