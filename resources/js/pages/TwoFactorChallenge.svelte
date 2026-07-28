<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import Intro from "$/app/ui/auth/Intro.svelte";
    import AuthLayout from "$/app/ui/layouts/AuthLayout.svelte";
    import CompleteTwoFactorChallenge from "$/generated/actions/App/Domain/Identity/Actions/CompleteTwoFactorChallenge";
    import ResendTwoFactorChallengeCode from "$/generated/actions/App/Domain/Identity/Actions/ResendTwoFactorChallengeCode";
    import { m } from "$/paraglide/messages";
    import Froggy from "$/shared/assets/froggy.svg";
    import { HistoryView } from "$/shared/inertia/history-view.svelte";
    import { useActionRateLimit } from "$/shared/inertia/use-action-rate-limit.svelte";
    import { pushSubscription } from "$/shared/lib/push/subscription.svelte";
    import { createActionBanner } from "$/shared/ui/ActionBanner.svelte";
    import Button from "$/shared/ui/Button.svelte";
    import OneTimePasswordInput from "$/shared/ui/OneTimePasswordInput.svelte";
    import { toaster } from "$/shared/ui/toaster";

    const id = $props.id();

    const resendTimer = useActionRateLimit(
        ResendTwoFactorChallengeCode.definition
    );
    const consumeTimer = useActionRateLimit(
        CompleteTwoFactorChallenge.definition
    );

    const view = new HistoryView(null, { viewTransition: true });

    async function handleSuccessfulLogin() {
        await pushSubscription.synchronize();

        if (pushSubscription.warnings.needsConfiguration) {
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
            <img src={Froggy} width={90} height={85} alt="" />
        {/snippet}
    </Intro>

    <Form action={ResendTwoFactorChallengeCode()} id="{id}-resend-form" hidden
    ></Form>

    <Form
        action={CompleteTwoFactorChallenge()}
        class="mt-13 space-y-3.5"
        onError={(error) => {
            if (error.password) {
                toaster.error(error.password);
            }
        }}
        onSuccess={handleSuccessfulLogin}
        let:processing
        let:errors
    >
        <OneTimePasswordInput
            name="password"
            error={Boolean(errors.password)}
        />

        <p class="text-center text-ms">
            {m["2fa.no-code"]()}
            <button
                class="font-semibold text-brand"
                form="{id}-resend-form"
                disabled={resendTimer.running}
            >
                {#if resendTimer.running}
                    {m["2fa.resend-in"]({ seconds: resendTimer.secondsLeft })}
                {:else}
                    {m["2fa.resend"]()}
                {/if}
            </button>
        </p>

        <Button disabled={consumeTimer.running || processing}>
            {#if consumeTimer.running}
                {m["2fa.continue-in"]({ seconds: consumeTimer.secondsLeft })}
            {:else}
                {m["2fa.continue"]()}
            {/if}
        </Button>
    </Form>
</AuthLayout>
