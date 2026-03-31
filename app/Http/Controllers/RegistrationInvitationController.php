<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\RegistrationInvitationDto;
use App\Domain\Auth\Mail\InviteToJodi;
use App\Models\RegistrationInvitation;
use App\Support\Http\JodiRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class RegistrationInvitationController extends Controller
{
    public function getAll(JodiRequest $request)
    {
        return response()->json(RegistrationInvitationDto::collect($this->user()->invitations->all()));
    }

    public function get(JodiRequest $request, RegistrationInvitation $invitation)
    {
        return response()->json(RegistrationInvitationDto::fromModel($invitation));
    }

    public function invite(JodiRequest $request)
    {
        $data = $request->validate([
            'email' => 'required|email|unique:registration_invitations',
        ]);

        $code = strtolower((string) Str::ulid());
        $expires_at = now()->addDays(config('auth.signup.invite_duration_in_days'));

        $this->user()->invitations()->create([
            'email' => $data['email'],
            'code' => $code,
            'expires_at' => $expires_at,
        ]);

        Mail::to($data['email'])->send(new InviteToJodi(
            $this->user(),
            URL::temporarySignedRoute('signup', $expires_at, ['code' => $code])
        ));

        return back();
    }

    public function destroy(JodiRequest $request, RegistrationInvitation $invitation)
    {
        Gate::authorize('destroy', $invitation);

        $invitation->delete();

        return back();
    }
}
