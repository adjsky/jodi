<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import Intro from "$/app/components/auth/Intro.svelte";
    import AuthLayout from "$/app/layouts/AuthLayout.svelte";
    import Froggy from "$/shared/ui/assets/froggy.svg";
    import Button from "$/shared/ui/primitives/Button.svelte";
    import OneTimePasswordInput from "$/shared/ui/primitives/OneTimePasswordInput.svelte";
    import { toaster } from "$/shared/utils/toaster";
    import { useActionRateLimit } from "$/shared/utils/use-action-rate-limit.svelte";
    import { consume, resend } from "$actions/TwoFactorChallengeController";

    const id = $props.id();

    const resendTimer = useActionRateLimit(resend.definition);
    const consumeTimer = useActionRateLimit(consume.definition);
</script>

<AuthLayout>
    <Intro class="mt-24" title="Enter 6-digit code">
        {#snippet icon()}
            <img src={Froggy} width={90} height={85} alt="" />
        {/snippet}
    </Intro>

    <Form action={resend()} id="{id}-resend-form" hidden></Form>

    <Form
        action={consume()}
        class="mt-24 space-y-4"
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

        <p class="text-center text-sm">
            You didn't receive any code?
            <button
                class="font-medium text-brand"
                form="{id}-resend-form"
                disabled={resendTimer.running}
            >
                {#if resendTimer.running}
                    Resend in {resendTimer.secondsLeft}s
                {:else}
                    Resend code
                {/if}
            </button>
        </p>

        <Button loading={processing} disabled={consumeTimer.running}>
            {#if consumeTimer.running}
                Continue in {consumeTimer.secondsLeft}s
            {:else}
                Continue
            {/if}
        </Button>
    </Form>
</AuthLayout>
