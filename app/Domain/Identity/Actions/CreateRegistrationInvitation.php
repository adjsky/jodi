<?php

declare(strict_types=1);

namespace App\Domain\Identity\Actions;

use App\Domain\Identity\Data\Input\CreateRegistrationInvitationData;
use App\Domain\Identity\Data\Output\RegistrationInvitationData;
use App\Domain\Identity\Mail\InviteToJodi;
use App\Domain\Identity\Models\RegistrationInvitation;
use App\Domain\Identity\Models\User;
use App\Support\Actions\JodiAction;
use App\Support\Http\JodiRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class CreateRegistrationInvitation extends JodiAction
{
    public function handle(User $user, CreateRegistrationInvitationData $data): RegistrationInvitation
    {
        $code = strtolower((string) Str::ulid());
        $expires_at = now()->addDays(config('auth.signup.invite_duration_in_days'));

        $invitation = $user->invitations()->create([
            'email' => $data->email,
            'code' => $code,
            'expires_at' => $expires_at,
        ]);

        Mail::to($data->email)->send(new InviteToJodi(
            $user,
            URL::temporarySignedRoute('signup', $expires_at, ['code' => $code])
        ));

        return $invitation;
    }

    public function asController(JodiRequest $request): RedirectResponse
    {
        $invitation = $this->handle(
            $this->user(),
            CreateRegistrationInvitationData::from($request)
        );

        $request->setFlash(
            'invitation',
            RegistrationInvitationData::fromModel($invitation)
        );

        return back();
    }
}
