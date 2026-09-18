<?php

declare(strict_types=1);

use App\Domain\Identity\Actions\CheckRegistrationInvitation;
use App\Domain\Identity\Actions\CompleteInvitationRegistrationChallenge;
use App\Domain\Identity\Actions\CompleteLoginChallenge;
use App\Domain\Identity\Actions\CreateInvitationRegistrationChallenge;
use App\Domain\Identity\Actions\CreateLoginChallenge;
use App\Domain\Identity\Actions\CreateRegistrationInvitation;
use App\Domain\Identity\Actions\DestroyRegistrationInvitation;
use App\Domain\Identity\Actions\GetCurrentSession;
use App\Domain\Identity\Actions\ListFriends;
use App\Domain\Identity\Actions\ListRegistrationInvitations;
use App\Domain\Identity\Actions\LogoutUser;
use App\Domain\Identity\Actions\ResendInvitationRegistrationChallenge;
use App\Domain\Identity\Actions\ResendLoginChallenge;
use App\Domain\Identity\Actions\UpdateUser;
use App\Domain\Identity\Actions\UpsertPushSubscription;
use App\Infrastructure\Firebase\Actions\GetFirebaseWebPushConfiguration;
use Illuminate\Support\Facades\Route;

Route::get('/firebase/web-push-configuration', GetFirebaseWebPushConfiguration::class);

Route::prefix('/auth')->group(function () {
    Route::prefix('/login/challenges')->group(function () {
        Route::post('/', CreateLoginChallenge::class);
        Route::post('/{id}/complete', CompleteLoginChallenge::class);
        Route::post('/{id}/resend', ResendLoginChallenge::class);
    });

    Route::prefix('/invitation-registration/challenges')->group(function () {
        Route::get('/{code}/validity', CheckRegistrationInvitation::class);
        Route::post('/{code}', CreateInvitationRegistrationChallenge::class);
        Route::post('/{id}/complete', CompleteInvitationRegistrationChallenge::class);
        Route::post('/{id}/resend', ResendInvitationRegistrationChallenge::class);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', LogoutUser::class);

    Route::prefix('/me')->group(function () {
        Route::get('/', GetCurrentSession::class);
        Route::patch('/', UpdateUser::class);

        Route::put('/push-subscription', UpsertPushSubscription::class);

        Route::prefix('/invitations')->group(function () {
            Route::post('/', CreateRegistrationInvitation::class);
            Route::get('/', ListRegistrationInvitations::class);
            Route::delete('/{invitation}', DestroyRegistrationInvitation::class);
        });

        Route::prefix('/friends')->group(function () {
            Route::get('/', ListFriends::class);
        });
    });
});

// Route::middleware('auth:sanctum')->group(function (): void {

//     Route::prefix('/categories')->group(function (): void {
//         Route::get('/', ListCategories::class);
//         Route::post('/', CreateCategory::class);
//         Route::delete('/{category}', DestroyCategory::class);
//     });

//     Route::prefix('/todos')->group(function (): void {
//         Route::post('/', CreateTodo::class);
//         Route::post('/reorder', ReorderTodos::class);
//         Route::put('/{todo}', UpdateTodo::class);
//         Route::delete('/{todo}', DestroyTodo::class);
//         Route::post('/{todo}/complete', CompleteTodo::class);
//     });

//     Route::prefix('/events')->group(function (): void {
//         Route::post('/', CreateEvent::class);
//         Route::put('/{event}', UpdateEvent::class);
//         Route::delete('/{event}', DestroyEvent::class);
//         Route::get('/calendar/{year}', ListCalendarEvents::class);
//     });
// });
