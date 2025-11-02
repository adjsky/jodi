<script>
    import { Form } from "@inertiajs/svelte";
    import Intro from "$/app/components/auth/Intro.svelte";
    import AuthLayout from "$/app/layouts/AuthLayout.svelte";
    import Froggy from "$/shared/ui/assets/froggy.svg";
    import Button from "$/shared/ui/primitives/Button.svelte";
    import OneTimePasswordInput from "$/shared/ui/primitives/OneTimePasswordInput.svelte";
    import { toaster } from "$/shared/utils/toaster";
    import { consume, resend } from "$actions/TwoFactorChallengeController";

    const id = $props.id();
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
        <OneTimePasswordInput name="password" error={errors.password} />

        <p class="text-center text-sm">
            You didn't receive any code?
            <button class="font-medium text-brand" form="{id}-resend-form">
                Resend code
            </button>
        </p>

        <Button loading={processing}>Continue</Button>
    </Form>
</AuthLayout>
