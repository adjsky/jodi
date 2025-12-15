<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\RegistrationInvitationDto;
use App\Domain\Auth\Mail\InviteToJodi;
use App\Models\RegistrationInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class RegistrationInvitationController extends Controller
{
    public function index(Request $request)
    {
        return inertia('CurrentUser/Invitations', [
            'invitations' => RegistrationInvitationDto::collect($this->user()->invitations->all()),
        ]);
    }

    public function show(Request $request, RegistrationInvitation $invitation)
    {
        return inertia('CurrentUser/Invitation', [
            'invitation' => RegistrationInvitationDto::fromModel($invitation),
            'shareUrl' => URL::temporarySignedRoute(
                'signup',
                $invitation->expires_at,
                ['code' => $invitation->code]
            ),
        ]);
    }

    public function invite(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
        ]);

        $code = strtolower((string) Str::ulid());
        $expires_at = now()->addDays(config('auth.signup.invite_duration_in_days'));

        $this->user()->invitations()->create([
            'email' => $data['email'],
            'code' => $code,
            'expires_at' => $expires_at,
        ]);

        Mail::to($data['email'])->send(new InviteToJodi($this->user()->email,
            URL::temporarySignedRoute('signup', $expires_at, ['code' => $code])
        ));

        return back()->with(['success' => __('Waiting for user registration.')]);
    }

    public function destroy(Request $request, RegistrationInvitation $invitation)
    {
        Gate::authorize('destroy', $invitation);

        $invitation->delete();

        return to_route('invitations')->with(['success' => __('Successfuly deleted invitation.')]);
    }
}
