<script lang="ts">
    import * as NetworkStatus from "#app/init/network-status.ts";

    const { children } = $props();

    NetworkStatus.init();
</script>

{@render children()}
