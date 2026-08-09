<?php

declare(strict_types=1);

namespace App\Domain\Identity\Actions;

use App\Domain\Identity\Data\Input\CompleteTwoFactorChallengeData;
use App\Domain\Identity\Enums\OtpPurpose;
use App\Domain\Identity\Exceptions\InvalidOtpException;
use App\Domain\Identity\Exceptions\NoUserException;
use App\Domain\Identity\Exceptions\OtpExpiredException;
use App\Domain\Identity\Models\User;
use App\Domain\Identity\Services\OtpService;
use App\Domain\Identity\Services\ThrottleService;
use App\Support\Actions\JodiAction;
use App\Support\Http\JodiRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class CompleteTwoFactorChallenge extends JodiAction
{
    public function __construct(
        private OtpService $otpService,
        private ThrottleService $throttleService
    ) {}

    public function handle(CompleteTwoFactorChallengeData $data, string $ip, string $email): User
    {
        $this->throttleService->throttleByIp(
            'consume',
            config('auth.2fa.throttle.consume.ip.attempts'),
            config('auth.2fa.throttle.consume.ip.decay_seconds'),
            $ip
        );

        $this->throttleService->throttleByEmail(
            'consume',
            config('auth.2fa.throttle.consume.email.attempts'),
            config('auth.2fa.throttle.consume.email.decay_seconds'),
            $email
        );

        $user = $this->otpService->consume(OtpPurpose::Login, $email, $data->password);

        return $user;
    }

    public function asController(JodiRequest $request): RedirectResponse
    {
        $email = $request->session()->get(sprintf('%s.email', config('auth.2fa.namespace')));

        if (! $email) {
            $request->setFlash('error', __('Log in first.'));

            return to_route('login');
        }

        try {
            $user = $this->handle(
                CompleteTwoFactorChallengeData::from($request),
                $request->ipOrFail(),
                $email
            );

            Auth::login($user, remember: true);

            $request->session()->regenerate();
            Inertia::clearHistory();

            return redirect()->intended();
        } catch (NoUserException|InvalidOtpException) {
            throw ValidationException::withMessages([
                'password' => __('The code is wrong.'),
            ]);
        } catch (OtpExpiredException) {
            $request->setFlash('error', __('The code is expired.'));

            return back();
        }
    }
}
