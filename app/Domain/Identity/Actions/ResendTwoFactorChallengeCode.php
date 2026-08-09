<?php

declare(strict_types=1);

namespace App\Domain\Identity\Actions;

use App\Domain\Identity\Enums\OtpPurpose;
use App\Domain\Identity\Models\User;
use App\Domain\Identity\Notifications;
use App\Domain\Identity\Services\OtpService;
use App\Domain\Identity\Services\ThrottleService;
use App\Support\Actions\JodiAction;
use App\Support\Http\JodiRequest;
use Illuminate\Http\RedirectResponse;

class ResendTwoFactorChallengeCode extends JodiAction
{
    public function __construct(
        private OtpService $otpService,
        private ThrottleService $throttleService
    ) {}

    public function handle(string $ip, string $email): void
    {
        $this->throttleService->throttleByIp(
            'resend',
            config('auth.2fa.throttle.resend.ip.attempts'),
            config('auth.2fa.throttle.resend.ip.decay_seconds'),
            $ip
        );

        $this->throttleService->throttleByEmail(
            'resend',
            config('auth.2fa.throttle.resend.email.attempts'),
            config('auth.2fa.throttle.resend.email.decay_seconds'),
            $email
        );

        $user = User::where(['email' => $email])->first();

        if ($user) {
            $password = $this->otpService->generate(OtpPurpose::Login, $user);
            $user->notify(new Notifications\OneTimeLoginCode($password));
        }
    }

    public function asController(JodiRequest $request): RedirectResponse
    {
        $email = $request->session()->get(
            sprintf('%s.email', config('auth.2fa.namespace'))
        );

        if (! $email) {
            $request->setFlash('error', __('Log in first.'));

            return to_route('login');
        }

        $this->handle($request->ipOrFail(), $email);

        $request->setFlash('success', __('The code has been sent.'));

        return back();
    }
}
