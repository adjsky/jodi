<script lang="ts">
    import { Popover } from "@ark-ui/svelte";
    import { Circle } from "@lucide/svelte";
    import { link } from "$/shared/inertia/link";
    import { noop } from "$/shared/lib/function";
    import ToolbarAction from "$/shared/ui/ToolbarAction.svelte";

    import type { LinkParameters } from "$/shared/inertia/link";

    type Props = LinkParameters & {
        tooltip: string;
        current: string | null;
        name?: string;
    };

    let { tooltip, current = $bindable(), name, ...options }: Props = $props();

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
                {@const inertia = name ? (noop as never) : link}
                <!-- svelte-ignore a11y_consider_explicit_label -->
                <button
                    {@attach inertia(() => ({
                        ...options,
                        data: { color: color == "transparent" ? null : color },
                        showProgress: false
                    }))}
                    onclick={() => {
                        current = color;
                        open = false;
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

{#if name}
    <input hidden bind:value={current} {name} />
{/if}
