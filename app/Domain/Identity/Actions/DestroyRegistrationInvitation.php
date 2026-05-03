<?php

declare(strict_types=1);

namespace App\Domain\Identity\Actions;

use App\Domain\Identity\Models\RegistrationInvitation;
use App\Support\Actions\JodiAction;
use App\Support\Http\JodiRequest;
use Illuminate\Http\RedirectResponse;

class DestroyRegistrationInvitation extends JodiAction
{
    public function handle(RegistrationInvitation $invitation): void
    {
        $invitation->delete();
    }

    public function authorize(JodiRequest $request): bool
    {
        return $this->user()->can('destroy', $request->invitation);
    }

    public function asController(RegistrationInvitation $invitation): RedirectResponse
    {
        $this->handle($invitation);

        return back();
    }
}
