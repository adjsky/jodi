<script lang="ts">
    import { m } from "#paraglide/messages.js";
    import { ScreenView } from "#shared/ui/composites/screen-view/mod.ts";

    import type { ViewProps } from "../model/types.ts";

    let { open = $bindable(), user }: ViewProps = $props();
</script>

<ScreenView.Overlay bind:open>
    <ScreenView.Header title={m["current-user.account.email"]()} />
    <ScreenView.Content>{user.email}</ScreenView.Content>
</ScreenView.Overlay>
