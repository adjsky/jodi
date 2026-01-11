<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CurrentUserController extends Controller
{
    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|min:1|max:36',
            'email' => 'sometimes|email',
            'preferences' => 'sometimes|array',
        ]);

        if ($request->has('preferences')) {
            $data['preferences'] = [
                ...$this->user()->preferences,
                ...$data['preferences'],
            ];
        }

        $this->user()->update($data);

        return back();
    }
}
