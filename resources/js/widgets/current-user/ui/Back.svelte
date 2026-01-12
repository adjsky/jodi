<script lang="ts" module>
    export { back };
</script>

<script lang="ts">
    import { ChevronLeft } from "@lucide/svelte";

    import { view } from "../model/view";
</script>

{#snippet back()}
    <button class="p-2" onclick={() => view.back()}>
        <ChevronLeft class="text-4xl" />
    </button>
{/snippet}
