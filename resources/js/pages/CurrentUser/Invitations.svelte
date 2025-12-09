<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import { User } from "@lucide/svelte";
    import SettingsLayout from "$/app/ui/layouts/SettingLayout.svelte";
    import { invite } from "$/generated/actions/App/Http/Controllers/CurrentUserController";
    import { m } from "$/paraglide/messages";
    import { toastify } from "$/shared/inertia/visit/toastify.svelte";
    import Button from "$/shared/ui/Button.svelte";
    import TextField from "$/shared/ui/TextField.svelte";
</script>

<SettingsLayout title={m["current-user.account.invitations"]()}>
    <Form
        {...toastify()}
        action={invite()}
        class="flex flex-grow flex-col justify-between py-5"
        let:processing
        let:errors
    >
        <TextField
            type="email"
            name="email"
            placeholder={m["current-user.account.email"]()}
            error={errors.email}
            required
        >
            {#snippet indicator()}<User />{/snippet}
        </TextField>

        <Button type="submit" disabled={processing}>
            {m["current-user.invite"]()}
        </Button>
    </Form>
</SettingsLayout>
