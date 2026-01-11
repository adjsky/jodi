<script lang="ts">
    import { page } from "@inertiajs/svelte";
    import { m } from "$/paraglide/messages";
    import FloatingView from "$/shared/ui/FloatingView.svelte";

    import { back } from "./Back.svelte";

    const user = $derived($page.props.auth.user);
</script>

<FloatingView {back} title={m["current-user.account.name"]()}>
    {user.email}
</FloatingView>
