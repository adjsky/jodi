<script lang="ts">
    import { User } from "#entities/user/mod.ts";
    import { m } from "#paraglide/messages.js";
    import { api } from "#shared/api/client.ts";
    import { LocalSession } from "#shared/capabilities/local-session/mod.ts";
    import { toaster } from "#shared/ui/toaster.ts";
    import LogoutUser from "#wayfinder/actions/App/Domain/Identity/Actions/LogoutUser.ts";

    let isLoading = $state(false);
</script>

<User.Info.ActionRow
    {isLoading}
    class="justify-center text-red"
    onclick={async () => {
        isLoading = true;

        try {
            const { url, method } = LogoutUser();

            await api(url, { method });
            await LocalSession.end();
        } catch (e) {
            console.error(e);
            toaster.error(m["common.errors.unexpected"]());
        } finally {
            isLoading = false;
        }
    }}
>
    {m["current-user.log-out"]()}
</User.Info.ActionRow>
