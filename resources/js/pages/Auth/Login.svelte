<script>
    import { Form } from "@inertiajs/svelte";
    import { Mail } from "@lucide/svelte";
    import Intro from "$/app/components/auth/Intro.svelte";
    import AuthLayout from "$/app/layouts/AuthLayout.svelte";
    import Cat from "$/shared/ui/assets/cat.svg";
    import Button from "$/shared/ui/primitives/Button.svelte";
    import TextField from "$/shared/ui/primitives/TextField.svelte";
    import { store } from "$actions/LoginController";
</script>

<AuthLayout>
    <Intro class="mt-24" title="Log In">
        {#snippet icon()}
            <img src={Cat} width={94} height={85} alt="" />
        {/snippet}
    </Intro>

    <Form action={store()} class="mt-24 space-y-4" let:processing let:errors>
        <TextField
            type="email"
            name="email"
            placeholder="Enter your email"
            error={errors.email}
            required
        >
            {#snippet indicator()}<Mail />{/snippet}
        </TextField>

        <div class="space-y-1.5">
            <Button type="submit" loading={processing}>Continue</Button>
            <div
                class="flex items-center gap-3 text-sm font-semibold text-cream-400"
            >
                <div class="h-px w-full rounded-[1px] bg-current"></div>
                Or
                <div class="h-px w-full rounded-[1px] bg-current"></div>
            </div>
            <Button variant="secondary" type="button">
                Login with a passkey
            </Button>
        </div>
    </Form>
</AuthLayout>
