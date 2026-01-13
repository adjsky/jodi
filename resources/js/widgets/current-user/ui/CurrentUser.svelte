<script lang="ts">
    import { page } from "@inertiajs/svelte";
    import { User } from "$/entities/user";
    import { useNotificationsInitBanner } from "$/shared/lib/push-notifications";

    import { buildViewName, view } from "../model/view";
    import Settings from "./Settings.svelte";

    const user = $derived($page.props.auth.user);

    useNotificationsInitBanner(() => {
        return view.push(buildViewName("notifications"));
    });
</script>

<User.Avatar name={user.name} onclick={() => view.push("me")} />
<Settings />
