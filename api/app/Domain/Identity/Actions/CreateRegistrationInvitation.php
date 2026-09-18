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
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CreateRegistrationInvitation extends JodiAction
{
    public function handle(User $user, CreateRegistrationInvitationData $data, string $locale): RegistrationInvitation
    {
        $code = strtolower((string) Str::ulid());

        $invitation = $user->invitations()->create([
            'email' => $data->email,
            'code' => $code,
            'expires_at' => now()->addDays(RegistrationInvitation::EXPIRES_IN_X_DAYS),
        ]);

        Mail::to($data->email)
            ->locale($locale)
            ->send(new InviteToJodi(
                $user, sprintf('%s/invite/%s', config('app.url'), $code))
            );

        return $invitation;
    }

    public function asController(JodiRequest $request): JsonResponse
    {
        $invitation = $this->handle(
            $this->user(),
            CreateRegistrationInvitationData::from($request),
            app()->getLocale()
        );

        return response()->json(
            RegistrationInvitationData::from($invitation),
            201
        );
    }
}
