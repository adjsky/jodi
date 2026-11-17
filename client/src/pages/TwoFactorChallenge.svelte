<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import Intro from "$/app/ui/auth/Intro.svelte";
    import AuthLayout from "$/app/ui/layouts/AuthLayout.svelte";
    import CompleteTwoFactorChallenge from "$/generated/actions/App/Domain/Identity/Actions/CompleteTwoFactorChallenge";
    import ResendTwoFactorChallengeCode from "$/generated/actions/App/Domain/Identity/Actions/ResendTwoFactorChallengeCode";
    import { m } from "$/paraglide/messages";
    import Froggy from "$/shared/assets/froggy.svg";
    import { ActionRateLimit, HistoryView } from "$/shared/integrations/inertia";
    import { Push } from "$/shared/services/push";
    import { createActionBanner } from "$/shared/ui/ActionBanner.svelte";
    import Button from "$/shared/ui/Button.svelte";
    import OneTimePasswordInput from "$/shared/ui/OneTimePasswordInput.svelte";
    import { toaster } from "$/shared/ui/toaster";

    const id = $props.id();

    let is_resending = $state(false);

    const resend_timer = new ActionRateLimit(ResendTwoFactorChallengeCode.definition);
    const consume_timer = new ActionRateLimit(CompleteTwoFactorChallenge.definition);

    const view = new HistoryView(null, { viewTransition: true });

    async function handle_successful_login() {
        await Push.subscription.synchronize();

        if (Push.subscription.needs_configuration) {
            createActionBanner(m["push-notifications.configure.title"](), {
                id: "configure-push-notifications",
                action: m["push-notifications.configure.action"](),
                onAccept() {
                    return view.push("me/notifications");
                }
            });
        }
    }
</script>

<AuthLayout>
    <Intro title={m["2fa.title"]()}>
        {#snippet icon()}
            <img src={Froggy} width={90} height={85} alt="" decoding="async" fetchpriority="high" />
        {/snippet}
    </Intro>

    <Form
        action={ResendTwoFactorChallengeCode()}
        id="{id}-resend-form"
        showProgress={false}
        onStart={() => {
            is_resending = true;
        }}
        onFinish={() => {
            is_resending = false;
        }}
        hidden
    ></Form>

    <Form
        action={CompleteTwoFactorChallenge()}
        class="mt-13 space-y-3.5"
        onError={(error) => {
            if (error.password) {
                toaster.error(error.password);
            }
        }}
        showProgress={false}
        onSuccess={handle_successful_login}
    >
        {#snippet children({ processing, errors })}
            <OneTimePasswordInput name="password" error={Boolean(errors.password)} />

            <p class="text-center text-ms">
                {m["2fa.no-code"]()}
                <button
                    class={[
                        "font-semibold text-brand disabled:cursor-not-allowed",
                        is_resending && "animate-pulse"
                    ]}
                    form="{id}-resend-form"
                    disabled={resend_timer.is_running || is_resending}
                    aria-busy={is_resending}
                >
                    {#if resend_timer.is_running}
                        {m["2fa.resend-in"]({
                            seconds: resend_timer.seconds_left
                        })}
                    {:else}
                        {m["2fa.resend"]()}
                    {/if}
                </button>
            </p>

            <Button type="submit" disabled={consume_timer.is_running} loading={processing}>
                {#if consume_timer.is_running}
                    {m["2fa.continue-in"]({
                        seconds: consume_timer.seconds_left
                    })}
                {:else}
                    {m["2fa.continue"]()}
                {/if}
            </Button>
        {/snippet}
    </Form>
</AuthLayout>
