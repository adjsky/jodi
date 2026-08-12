<script lang="ts">
    import { Portal } from "@ark-ui/svelte";

    import { raise } from "../lib/exception/raise";

    import type { PortalProps } from "@ark-ui/svelte";
    import type { Except } from "type-fest";

    type Props = Except<PortalProps, "container">;

    const { children, disabled }: Props = $props();
</script>

<Portal
    {disabled}
    {children}
    container={document.querySelector<HTMLElement>("#portal-root") ??
        raise("Failed to locate portal root.")}
/>
