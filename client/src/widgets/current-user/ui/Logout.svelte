<script lang="ts">
    import { User } from "#entities/user/mod.ts";
    import { m } from "#paraglide/messages.js";
    import { api } from "#shared/api/client.ts";
    import { LocalSession } from "#shared/capabilities/local-session/mod.ts";
    import { toaster } from "#shared/ui/toaster.ts";
    import LogoutUser from "#wayfinder/actions/App/Domain/Identity/Actions/LogoutUser.ts";

    let is_loading = $state(false);
</script>

<User.Info.ActionRow
    {is_loading}
    class="justify-center text-red"
    onclick={async () => {
        is_loading = true;

        try {
            const { url, method } = LogoutUser();

            await api(url, { method });
            await LocalSession.end();
        } catch (e) {
            console.error("Failed to log out: ", e);
            toaster.error(m["common.errors.unexpected"]());
        } finally {
            is_loading = false;
        }
    }}
>
    {m["current-user.log-out"]()}
</User.Info.ActionRow>
