<script lang="ts">
    import { Popover } from "@ark-ui/svelte";
    import { Circle } from "@lucide/svelte";
    import { dispatchInput } from "$/shared/lib/dom/dispatch-input";
    import ToolbarAction from "$/shared/ui/ToolbarAction.svelte";
    import { tick } from "svelte";

    type Props = {
        tooltip: string;
        current?: string | null;
        name: string;
    };

    let { tooltip, current = $bindable(), name }: Props = $props();

    let announcerInput = $state<HTMLInputElement | null>(null);
    let open = $state(false);

    const colors = [
        "transparent",
        "#C85A54",
        "#E07A5F",
        "#528A66",
        "#3B8EA5",
        "#5B72A4",
        "#9E5A9B"
    ];
</script>

<Popover.Root bind:open>
    <Popover.Trigger>
        {#snippet asChild(props)}
            <ToolbarAction
                {...props()}
                {tooltip}
                class="p-3.5 text-xl disabled:not-data-loading:text-cream-400"
            >
                {#if current}
                    <span
                        class="block size-5 rounded-full outline-1 outline-cream-950"
                        style="background: {current};"
                    ></span>
                {:else}
                    <Circle />
                {/if}
            </ToolbarAction>
        {/snippet}
    </Popover.Trigger>
    <Popover.Positioner>
        <Popover.Content
            class="flex rounded-full bg-white px-1 outline outline-cream-950"
        >
            {#each colors as color (color)}
                <button
                    type="button"
                    aria-label="Update color"
                    onclick={async () => {
                        current = color == "transparent" ? null : color;
                        open = false;
                        await tick();
                        dispatchInput(announcerInput);
                    }}
                    class="flex h-10 w-11.25 items-center justify-center"
                >
                    <span
                        class={[
                            "flex size-6 rounded-full",
                            color == "transparent" && "border border-cream-950"
                        ]}
                        style="background: {color}"
                    ></span>
                </button>
            {/each}
        </Popover.Content>
    </Popover.Positioner>
</Popover.Root>

<input bind:this={announcerInput} hidden value={current} {name} />
