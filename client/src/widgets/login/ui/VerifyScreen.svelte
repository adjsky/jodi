<script lang="ts">
    import {
        PushSubscriptionSchema,
        SessionCache,
        UserSchema
    } from "#entities/user/mod.ts";
    import { m } from "#paraglide/messages.js";
    import { AuthTokenStorage } from "#shared/capabilities/auth-token-storage/mod.ts";
    import { Overlay } from "#shared/capabilities/overlay/mod.svelte.ts";
    import { VirtualKeyboard } from "#shared/capabilities/virtual-keyboard/mod.ts";
    import { PLATFORM } from "#shared/cfg/constants.ts";
    import { raise } from "#shared/lib/exception/raise.ts";
    import { LastMatching } from "#shared/lib/svelte/last-matching.svelte.ts";
    import { Timer } from "#shared/lib/svelte/timer.svelte.ts";
    import ApiForm from "#shared/ui/ApiForm.svelte";
    import Button from "#shared/ui/Button.svelte";
    import { ScreenView } from "#shared/ui/composites/screen-view/mod.ts";
    import OneTimePasswordInput from "#shared/ui/OneTimePasswordInput.svelte";
    import { toaster } from "#shared/ui/toaster.ts";
    import CompleteLoginChallenge from "#wayfinder/actions/App/Domain/Identity/Actions/CompleteLoginChallenge.ts";
    import ResendLoginChallenge from "#wayfinder/actions/App/Domain/Identity/Actions/ResendLoginChallenge.ts";
    import { goto } from "$app/navigation";
    import { resolve } from "$app/paths";
    import * as z from "zod";

    const id = $props.id();

    const challenge = new LastMatching(
        () => Overlay.meta?.loginchallenge,
        (challenge) => challenge !== undefined
    );

    const resendThrottleTimer = new Timer();
    const completeThrottleTimer = new Timer();

    let isResendingLoginChallenge = $state(false);
</script>

<ScreenView.Overlay
    bind:open={() => Overlay.isOpen("login/verify"), () => Overlay.close()}
    {@attach VirtualKeyboard.retainFocus()}
    onExitComplete={() => challenge.reset()}
>
    <ScreenView.Header shape="flat" />
    <ScreenView.Content class="mt-2">
        <h1 class="text-4xl font-bold">{m["login/verify.title"]()}</h1>
        <p class="mt-3 break-all text-cream-700">
            <span class="font-medium">{m["login/verify.sent-to"]()}</span>
            <span class="font-bold">{challenge.current?.email}</span>
        </p>

        <ApiForm
            action={CompleteLoginChallenge({
                id:
                    challenge.current?.id ??
                    raise("Invalid login/verify metadata.")
            })}
            class="mt-7 flex grow flex-col"
            inputSchema={z.object({
                code: z
                    .string()
                    .length(6, { message: m["login/verify.errors.code"]() })
            })}
            dataSchema={z.object({
                token: PLATFORM === "web" ? z.null() : z.string(),
                user: UserSchema,
                pushSubscription: PushSubscriptionSchema.nullable()
            })}
            onValidationError={(errors) => {
                if (errors.code) {
                    toaster.error(errors.code[0]);
                }
            }}
            onRateLimit={(retryAfter) => {
                completeThrottleTimer.start(retryAfter);
            }}
            onSuccess={async (data) => {
                await SessionCache.set({
                    user: data.user,
                    pushSubscription: data.pushSubscription
                });

                if (data.token) {
                    await AuthTokenStorage.set(data.token);
                }

                await goto(resolve("/(app)"));
            }}
        >
            {#snippet children({ errors, submitting })}
                <OneTimePasswordInput
                    name="code"
                    error={Boolean(errors.code)}
                />

                <p class="mt-6 text-ms">
                    <span class="font-medium text-cream-700">
                        {m["login/verify.no-code"]()}
                    </span>
                    <button
                        class={[
                            "font-bold text-brand disabled:cursor-not-allowed",
                            isResendingLoginChallenge && "animate-pulse"
                        ]}
                        form="{id}-resend-form"
                        disabled={resendThrottleTimer.isRunning ||
                            isResendingLoginChallenge}
                        aria-busy={isResendingLoginChallenge}
                    >
                        {#if resendThrottleTimer.isRunning}
                            {m["login/verify.resend-in"]({
                                seconds: resendThrottleTimer.secondsLeft
                            })}
                        {:else}
                            {m["login/verify.resend"]()}
                        {/if}
                    </button>
                </p>

                <Button
                    type="submit"
                    class="mt-auto shrink-0"
                    disabled={completeThrottleTimer.isRunning}
                    loading={submitting}
                >
                    {#if completeThrottleTimer.isRunning}
                        {m["login/verify.continue-in"]({
                            seconds: completeThrottleTimer.secondsLeft
                        })}
                    {:else}
                        {m["login/verify.continue"]()}
                    {/if}
                </Button>
            {/snippet}
        </ApiForm>

        <ApiForm
            action={ResendLoginChallenge({
                id:
                    challenge.current?.id ??
                    raise("Invalid login/verify metadata.")
            })}
            id="{id}-resend-form"
            onSubmit={() => {
                isResendingLoginChallenge = true;
            }}
            onFinish={() => {
                isResendingLoginChallenge = false;
            }}
            onSuccess={() => {
                toaster.success(m["login/verify.sent-code"]());
            }}
            onRateLimit={(retryAfter) => resendThrottleTimer.start(retryAfter)}
            hidden
        ></ApiForm>
    </ScreenView.Content>
</ScreenView.Overlay>
