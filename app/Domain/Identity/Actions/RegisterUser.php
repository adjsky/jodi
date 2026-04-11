<?php

declare(strict_types=1);

namespace App\Domain\Identity\Actions;

use App\Domain\Identity\Data\Input\RegisterUserData;
use App\Domain\Identity\Models\RegistrationInvitation;
use App\Domain\Identity\Models\User;
use App\Domain\Identity\ValueObjects\UserPreferences;
use App\Support\Actions\Action;
use App\Support\Http\JodiRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RegisterUser extends Action
{
    public function handle(RegisterUserData $data, string $code, ?string $timezone): User
    {
        return DB::transaction(function () use ($code, $data, $timezone) {
            $invitation = RegistrationInvitation::where('code', '=', $code)->firstOrFail();

            $user = User::create([
                'email' => $invitation->email,
                'name' => $data->name,
                'preferences' => UserPreferences::from([
                    ...config('jodi.preferences'),
                    'locale' => app()->getLocale(),
                    'timezone' => $timezone ?? config('jodi.preferences.timezone'),
                ]),
            ]);

            $inviter = User::findOrFail($invitation->inviter_user_id);

            $user->friends()->attach($inviter->id);
            $inviter->friends()->attach($user->id);

            $invitation->delete();

            return $user;
        });
    }

    public function asController(JodiRequest $request, string $code): RedirectResponse
    {
        $user = $this->handle(RegisterUserData::from($request), $code, $request->timezone());

        Auth::login($user, remember: true);
        $request->session()->regenerate();
        Inertia::clearHistory();

        return to_route('home');
    }
}
