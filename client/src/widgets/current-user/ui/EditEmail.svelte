<script lang="ts">
    import { page } from "@inertiajs/svelte";
    import { m } from "$/paraglide/messages";
    import { ScreenView } from "$/shared/composites/screen-view";

    import type { ViewProps } from "../model/view";

    let { open = $bindable() }: ViewProps = $props();

    const user = $derived(page.props.auth.user);
</script>

<ScreenView.Overlay bind:open>
    <ScreenView.Header title={m["current-user.account.email"]()} />
    <ScreenView.Content>{user.email}</ScreenView.Content>
</ScreenView.Overlay>
