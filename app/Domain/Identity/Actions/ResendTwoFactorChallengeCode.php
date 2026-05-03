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

    public function handle(string $email): void
    {
        $this->throttleService->throttle(
            'resend-otp',
            1,
            config('auth.2fa.resend_otp_throttle'),
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
            sprintf('%s.email', config('auth.2fa.session_key'))
        );

        if (! $email) {
            return to_route('login')->with(['error' => __('Log in first.')]);
        }

        $this->handle($email);

        $request->setFlash('success', __('The code has been sent.'));

        return back();
    }
}
