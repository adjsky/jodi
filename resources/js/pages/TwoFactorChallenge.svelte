<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import Intro from "$/app/ui/auth/Intro.svelte";
    import AuthLayout from "$/app/ui/layouts/AuthLayout.svelte";
    import { m } from "$/paraglide/messages";
    import Froggy from "$/shared/assets/froggy.svg";
    import { useActionRateLimit } from "$/shared/inertia/use-action-rate-limit.svelte";
    import { toastify } from "$/shared/inertia/visit/toastify.svelte";
    import { toaster } from "$/shared/lib/toast";
    import Button from "$/shared/ui/Button.svelte";
    import OneTimePasswordInput from "$/shared/ui/OneTimePasswordInput.svelte";
    import { consume, resend } from "$actions/TwoFactorChallengeController";

    const id = $props.id();

    const resendTimer = useActionRateLimit(resend.definition);
    const consumeTimer = useActionRateLimit(consume.definition);
</script>

<AuthLayout>
    <Intro class="mt-24" title={m["2fa.title"]()}>
        {#snippet icon()}
            <img src={Froggy} width={90} height={85} alt="" />
        {/snippet}
    </Intro>

    <Form {...toastify()} action={resend()} id="{id}-resend-form" hidden></Form>

    <Form
        {...toastify()}
        action={consume()}
        class="mt-13 space-y-3.5"
        onError={(error) => {
            if (error.password) {
                toaster.error({ title: error.password });
            }
        }}
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
