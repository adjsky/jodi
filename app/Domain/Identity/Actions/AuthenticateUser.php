<?php

declare(strict_types=1);

namespace App\Domain\Identity\Actions;

use App\Domain\Identity\Data\Input\AuthenticateUserData;
use App\Domain\Identity\Enums\OtpPurpose;
use App\Domain\Identity\Models\User;
use App\Domain\Identity\Notifications;
use App\Domain\Identity\Services\OtpService;
use App\Domain\Identity\Services\ThrottleService;
use App\Support\Actions\JodiAction;
use App\Support\Http\JodiRequest;
use Illuminate\Http\RedirectResponse;

class AuthenticateUser extends JodiAction
{
    public function __construct(
        private OtpService $otpService,
        private ThrottleService $throttleService
    ) {}

    public function handle(AuthenticateUserData $data, string $ip): ?User
    {
        $this->throttleService->throttleByIp(
            'request',
            config('auth.2fa.throttle.request.ip.attempts'),
            config('auth.2fa.throttle.request.ip.decay_seconds'),
            $ip
        );

        $this->throttleService->throttleByEmail(
            'request',
            config('auth.2fa.throttle.request.email.attempts'),
            config('auth.2fa.throttle.request.email.decay_seconds'),
            $data->email
        );

        $user = User::whereEmail($data->email)->first();

        if ($user) {
            $password = $this->otpService->generate(OtpPurpose::Login, $user);
            $user->notify(new Notifications\OneTimeLoginCode($password));
        }

        return $user;
    }

    public function asController(JodiRequest $request): RedirectResponse
    {
        $data = AuthenticateUserData::from($request);

        $this->handle($data, $request->ipOrFail());

        $request->session()->put(
            sprintf('%s.email', config('auth.2fa.namespace')),
            $data->email
        );

        return to_route('two-factor-challenge');
    }
}
