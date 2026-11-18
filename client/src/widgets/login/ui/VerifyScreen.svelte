<script lang="ts">
    import { PushSubscriptionSchema, SessionCache, UserSchema } from "#entities/user/mod.ts";
    import { m } from "#paraglide/messages.js";
    import { AuthTokenStorage } from "#shared/capabilities/auth-token-storage/mod.ts";
    import { NotificationCoordinator } from "#shared/capabilities/notification-coordinator/mod.svelte.ts";
    import { Overlay } from "#shared/capabilities/overlay/mod.svelte.ts";
    import { VirtualKeyboard } from "#shared/capabilities/virtual-keyboard/mod.ts";
    import { PLATFORM } from "#shared/cfg/capacitor.ts";
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
    import z from "zod";

    const id = $props.id();

    const challenge = new LastMatching(
        () => Overlay.meta?.loginchallenge,
        (challenge) => challenge !== undefined
    );

    const resend_throttle_timer = new Timer();
    const complete_throttle_timer = new Timer();

    let is_resending_login_challenge = $state(false);
</script>

<ScreenView.Overlay
    bind:open={() => Overlay.is_open("login/verify"), () => Overlay.close()}
    {@attach VirtualKeyboard.retain_focus()}
    on_exit_complete={() => challenge.reset()}
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
                id: challenge.current?.id ?? raise("Invalid login/verify metadata.")
            })}
            class="mt-7 flex grow flex-col"
            input_schema={z.object({
                code: z.string().length(6, { error: m["login/verify.errors.code"]() })
            })}
            data_schema={z.object({
                token: PLATFORM === "web" ? z.null() : z.string(),
                user: UserSchema,
                pushSubscription: PushSubscriptionSchema.nullable()
            })}
            on_validation_error={(errors) => {
                if (errors.code) {
                    toaster.error(errors.code[0]);
                }
            }}
            on_rate_limit={(retry_after) => {
                complete_throttle_timer.start(retry_after);
            }}
            on_success={async (data) => {
                await SessionCache.set({
                    user: data.user,
                    pushSubscription: data.pushSubscription
                });

                if (data.token) {
                    await AuthTokenStorage.set(data.token);
                }

                await goto(resolve("/(app)"));
                await NotificationCoordinator.enable();
            }}
        >
            {#snippet children({ errors, submitting, is_dirty })}
                <OneTimePasswordInput name="code" error={Boolean(errors.code)} />

                <p class="mt-6 text-ms">
                    <span class="font-medium text-cream-700">
                        {m["login/verify.no-code"]()}
                    </span>
                    <button
                        class={[
                            "font-bold text-brand disabled:cursor-not-allowed",
                            is_resending_login_challenge && "animate-pulse"
                        ]}
                        form="{id}-resend-form"
                        disabled={resend_throttle_timer.is_running || is_resending_login_challenge}
                        aria-busy={is_resending_login_challenge}
                    >
                        {#if resend_throttle_timer.is_running}
                            {m["login/verify.resend-in"]({
                                seconds: resend_throttle_timer.seconds_left
                            })}
                        {:else}
                            {m["login/verify.resend"]()}
                        {/if}
                    </button>
                </p>

                <Button
                    type="submit"
                    class="mt-auto shrink-0"
                    disabled={complete_throttle_timer.is_running || !is_dirty}
                    loading={submitting}
                >
                    {#if complete_throttle_timer.is_running}
                        {m["login/verify.continue-in"]({
                            seconds: complete_throttle_timer.seconds_left
                        })}
                    {:else}
                        {m["login/verify.continue"]()}
                    {/if}
                </Button>
            {/snippet}
        </ApiForm>

        <ApiForm
            action={ResendLoginChallenge({
                id: challenge.current?.id ?? raise("Invalid login/verify metadata.")
            })}
            id="{id}-resend-form"
            on_submit={() => {
                is_resending_login_challenge = true;
            }}
            on_finish={() => {
                is_resending_login_challenge = false;
            }}
            on_success={() => {
                toaster.success(m["login/verify.sent-code"]());
            }}
            on_rate_limit={(retry_after) => resend_throttle_timer.start(retry_after)}
            hidden
        ></ApiForm>
    </ScreenView.Content>
</ScreenView.Overlay>
