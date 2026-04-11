<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import { Mail } from "@lucide/svelte";
    import Intro from "$/app/ui/auth/Intro.svelte";
    import AuthLayout from "$/app/ui/layouts/AuthLayout.svelte";
    import AuthenticateUser from "$/generated/actions/App/Domain/Identity/Actions/AuthenticateUser";
    import { m } from "$/paraglide/messages";
    import Cat from "$/shared/assets/cat.svg";
    import Button from "$/shared/ui/Button.svelte";
    import TextField from "$/shared/ui/TextField.svelte";
</script>

<AuthLayout>
    <Intro title={m["login.title"]()}>
        {#snippet icon()}
            <img src={Cat} width={94} height={85} alt="" />
        {/snippet}
    </Intro>

    <Form
        action={AuthenticateUser()}
        class="mt-13 space-y-4"
        let:processing
        let:errors
    >
        <TextField
            type="email"
            name="email"
            placeholder={m["login.email-placeholder"]()}
            error={errors.email}
            required
        >
            {#snippet indicator()}<Mail />{/snippet}
        </TextField>

        <div class="space-y-1.25">
            <Button type="submit" disabled={processing}>
                {m["login.submit"]()}
            </Button>
            <div
                class="flex items-center gap-3 text-sm leading-normal font-semibold text-cream-400"
            >
                <div class="h-px w-full rounded-[1px] bg-current"></div>
                {m["common.or"]()}
                <div class="h-px w-full rounded-[1px] bg-current"></div>
            </div>
            <Button variant="secondary" type="button" disabled>
                {m["login.with-passkey"]()}
            </Button>
        </div>
    </Form>
</AuthLayout>
