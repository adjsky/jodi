<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\RegistrationInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SignupController extends Controller
{
    public function show(Request $request, string $code)
    {
        if (! $request->hasValidSignature()) {
            return to_route('login')->with(['error' => __('URL signature is invalid. Request a new invitation.')]);
        }

        return inertia('Signup', ['code' => $code]);
    }

    public function signup(Request $request, string $code)
    {
        $data = $request->validate(['name' => 'required|string|min:1|max:36']);

        $user = DB::transaction(function () use ($code, $data) {
            $invitation = RegistrationInvitation::where('code', '=', $code)->firstOrFail();

            $user = User::create([
                'email' => $invitation->email,
                'name' => $data['name'],
                'preferences' => [
                    'locale' => app()->getLocale(),
                    'weekStartOn' => 'monday',
                    'notifications' => 'push',
                ],
            ]);

            $inviter = User::findOrFail($invitation->inviter_user_id);

            $user->friends()->attach($inviter->id);
            $inviter->friends()->attach($user->id);

            $invitation->delete();

            return $user;
        });

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        return to_route('home');
    }
}
