<script lang="ts">
    import { Popover } from "@ark-ui/svelte";
    import { Circle } from "@lucide/svelte";
    import { link } from "$/shared/inertia/link";
    import ToolbarAction from "$/shared/ui/ToolbarAction.svelte";

    import type { LinkComponentBaseProps, VisitOptions } from "@inertiajs/core";

    type Props = VisitOptions &
        Pick<LinkComponentBaseProps, "href"> & {
            tooltip: string;
            current: string | null;
        };

    const { tooltip, current, ...options }: Props = $props();

    let open = $state(false);

    const colors = [
        "transparent",
        "#CD2C54",
        "#FDEF5D",
        "#D85DFD",
        "#22FFA1",
        "#FFB022",
        "#5167F4"
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
                <!-- svelte-ignore a11y_consider_explicit_label -->
                <button
                    {@attach link(() => ({
                        ...options,
                        data: { color: color == "transparent" ? null : color },
                        showProgress: false
                    }))}
                    onclick={() => (open = false)}
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
