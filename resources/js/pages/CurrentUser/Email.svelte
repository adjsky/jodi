<script lang="ts">
    import { page } from "@inertiajs/svelte";
    import SettingsLayout from "$/app/ui/layouts/SettingLayout.svelte";
    import { m } from "$/paraglide/messages";

    const user = $derived($page.props.auth.user);
</script>

<SettingsLayout title={m["current-user.account.name"]()}>
    {user.email}
</SettingsLayout>
