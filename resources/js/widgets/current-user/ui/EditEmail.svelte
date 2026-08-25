<script lang="ts">
    import { page } from "@inertiajs/svelte";
    import { m } from "$/paraglide/messages";
    import { ScreenView } from "$/shared/composites/screen-view";

    import { back } from "./Back.svelte";

    const user = $derived($page.props.auth.user);
</script>

<ScreenView.Overlay>
    <ScreenView.Header {back} title={m["current-user.account.email"]()} />
    <ScreenView.Content>{user.email}</ScreenView.Content>
</ScreenView.Overlay>
