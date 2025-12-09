<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CurrentUserController extends Controller
{
    public function index(Request $request)
    {
        return inertia('CurrentUser', [
            'nInvitations' => $this->user()->invitations()->count(),
        ]);
    }

    public function name(Request $request)
    {
        return inertia('CurrentUser/Name');
    }

    public function email(Request $request)
    {
        return inertia('CurrentUser/Email');
    }

    public function friends(Request $request)
    {
        return inertia('CurrentUser/Friends');
    }

    public function language(Request $request)
    {
        return inertia('CurrentUser/Language');
    }

    public function weekStart(Request $request)
    {
        return inertia('CurrentUser/WeekStart');
    }

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

        return to_route('me')->with('success', __('All good.'));
    }
}
