<?php

declare(strict_types=1);

namespace App\Domain\Identity\Actions;

use App\Domain\Identity\Data\Input\AuthenticateUserData;
use App\Domain\Identity\Enums\OtpPurpose;
use App\Domain\Identity\Models\User;
use App\Domain\Identity\Notifications;
use App\Domain\Identity\Services\OtpService;
use App\Support\Actions\Action;
use App\Support\Http\JodiRequest;
use Illuminate\Http\RedirectResponse;

class AuthenticateUser extends Action
{
    public function __construct(private OtpService $otpService) {}

    public function handle(AuthenticateUserData $data): ?User
    {
        $user = User::whereEmail($data->email)->first();

        if ($user) {
            $password = $this->otpService->generate(OtpPurpose::Login, $user);
            $user->notify(new Notifications\OneTimeLoginCode($password));
        }

        return $user;
    }

    public function asController(JodiRequest $request): RedirectResponse
    {
        $this->handle(AuthenticateUserData::from($request));

        $request->session()->put(
            sprintf('%s.email', config('auth.2fa.session_key')),
            $request->input('email')
        );

        return to_route('two-factor-challenge');
    }
}
