<script lang="ts">
    import { Device } from "@capacitor/device";
    import { Form } from "@inertiajs/svelte";
    import Intro from "$/app/ui/auth/Intro.svelte";
    import AuthLayout from "$/app/ui/layouts/AuthLayout.svelte";
    import { m } from "$/paraglide/messages";
    import Froggy from "$/shared/assets/froggy.svg";
    import { DEVICE_ID_COOKIE } from "$/shared/cfg/constants";
    import { HistoryView } from "$/shared/inertia/history-view.svelte";
    import { useActionRateLimit } from "$/shared/inertia/use-action-rate-limit.svelte";
    import * as Cookie from "$/shared/lib/cookie";
    import * as PushSubscription from "$/shared/lib/push-subscription.svelte";
    import { toaster } from "$/shared/lib/toaster";
    import { createActionBanner } from "$/shared/ui/ActionBanner.svelte";
    import Button from "$/shared/ui/Button.svelte";
    import OneTimePasswordInput from "$/shared/ui/OneTimePasswordInput.svelte";
    import { consume, resend } from "$actions/TwoFactorChallengeController";

    const id = $props.id();

    const resendTimer = useActionRateLimit(resend.definition);
    const consumeTimer = useActionRateLimit(consume.definition);

    const view = new HistoryView(null, { viewTransition: true });

    async function handleSuccessfulLogin() {
        const { identifier } = await Device.getId();

        Cookie.set(DEVICE_ID_COOKIE, identifier, {
            maxAge: 34560000,
            sameSite: "lax"
        });

        await PushSubscription.synchronize();

        if (PushSubscription.warnings.needsConfiguration) {
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

    <Form action={resend()} id="{id}-resend-form" hidden></Form>

    <Form
        action={consume()}
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
