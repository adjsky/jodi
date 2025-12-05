<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CurrentUserController extends Controller
{
    public function show()
    {
        return inertia('CurrentUser');
    }

    public function update(Request $request)
    {
        $this->user()->update(
            $request->validate([
                'name' => 'sometimes|string|min:1|max:36',
                'email' => 'sometimes|email',
            ])
        );

        return back()->with('success', __('All good.'));
    }
}
