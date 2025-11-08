<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import { Mail } from "@lucide/svelte";
    import Intro from "$/app/components/auth/Intro.svelte";
    import AuthLayout from "$/app/layouts/AuthLayout.svelte";
    import { m } from "$/paraglide/messages";
    import Cat from "$/shared/ui/assets/cat.svg";
    import Button from "$/shared/ui/primitives/Button.svelte";
    import TextField from "$/shared/ui/primitives/TextField.svelte";
    import { login } from "$actions/LoginController";
</script>

<AuthLayout>
    <Intro class="mt-24" title={m["login.title"]()}>
        {#snippet icon()}
            <img src={Cat} width={94} height={85} alt="" />
        {/snippet}
    </Intro>

    <Form action={login()} class="mt-23 space-y-4" let:processing let:errors>
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
            <Button type="submit" loading={processing}>
                {m["login.submit"]()}
            </Button>
            <div
                class="flex items-center gap-3 text-sm font-semibold text-cream-400"
            >
                <div class="h-px w-full rounded-[1px] bg-current"></div>
                {m["or"]()}
                <div class="h-px w-full rounded-[1px] bg-current"></div>
            </div>
            <Button variant="secondary" type="button">
                {m["login.with-passkey"]()}
            </Button>
        </div>
    </Form>
</AuthLayout>
