<script lang="ts">
    import { page } from "@inertiajs/svelte";
    import { User } from "$/entities/user";
    import { pushSubscription } from "$/shared/lib/push/subscription.svelte";

    import { view } from "../model/view";
    import Settings from "./Settings.svelte";

    const user = $derived($page.props.auth.user);
</script>

<User.Avatar
    name={user.name}
    onclick={() => view.push("me")}
    warn={pushSubscription.warnings.needsConfiguration}
/>
<Settings />
