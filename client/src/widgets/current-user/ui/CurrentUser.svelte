<script lang="ts">
    import { SessionCache, User } from "#entities/user/mod.ts";
    import { Overlay } from "#shared/capabilities/overlay/mod.svelte.ts";

    import Settings from "./Settings.svelte";

    const session = SessionCache.retain();
    const user = $derived(session.current.user);
</script>

<User.Avatar
    name={user.name}
    onclick={() => Overlay.open("me")}
    // warn={Push.subscription.needsConfiguration}
/>
<Settings />
