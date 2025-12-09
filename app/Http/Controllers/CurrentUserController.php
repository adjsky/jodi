<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Auth\Mail\InviteToJodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class CurrentUserController extends Controller
{
    public function index(Request $request)
    {
        return inertia('CurrentUser');
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

    public function invitations(Request $request)
    {
        return inertia('CurrentUser/Invitations');
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

    public function invite(Request $request)
    {
        $data = $request->validate([
            'email' => 'email',
        ]);

        $code = strtolower((string) Str::ulid());
        $expires_at = now()->addDays(config('auth.signup.invite_duration_in_days'));

        $this->user()->invitations()->create([
            'email' => $data['email'],
            'code' => $code,
            'expires_at' => $expires_at,
        ]);

        Mail::to($data['email'])->send(new InviteToJodi($this->user()->email,
            URL::temporarySignedRoute('signup', $expires_at, ['code' => $code])
        ));

        return back();
    }
}
