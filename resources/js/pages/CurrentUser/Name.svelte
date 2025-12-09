<script lang="ts">
    import { Form, page } from "@inertiajs/svelte";
    import { User } from "@lucide/svelte";
    import SettingsLayout from "$/app/ui/layouts/SettingLayout.svelte";
    import { update } from "$/generated/actions/App/Http/Controllers/CurrentUserController";
    import { m } from "$/paraglide/messages";
    import { toastify } from "$/shared/inertia/visit/toastify.svelte";
    import Button from "$/shared/ui/Button.svelte";
    import TextField from "$/shared/ui/TextField.svelte";

    const user = $derived($page.props.auth.user);
</script>

<SettingsLayout title={m["current-user.account.name"]()}>
    <Form
        {...toastify()}
        action={update()}
        class="flex flex-grow flex-col justify-between py-5"
        let:processing
        let:errors
    >
        <TextField
            type="text"
            name="name"
            placeholder={m["current-user.account.name"]()}
            error={errors.name}
            defaultValue={user.name}
            required
        >
            {#snippet indicator()}<User />{/snippet}
        </TextField>

        <Button type="submit" disabled={processing}>
            {m["current-user.save"]()}
        </Button>
    </Form>
</SettingsLayout>
