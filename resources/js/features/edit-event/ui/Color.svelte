<script lang="ts">
    import { Popover } from "@ark-ui/svelte";
    import { Circle } from "@lucide/svelte";
    import { update } from "$/generated/actions/App/Http/Controllers/EventController";
    import { m } from "$/paraglide/messages";
    import { link } from "$/shared/inertia/link";

    import { optimistic, visitOptions } from "../cfg/inertia";
    import Action from "./Action.svelte";

    type Props = {
        event: App.Data.EventDto;
    };

    const { event }: Props = $props();

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

<Popover.Root>
    <Popover.Trigger>
        {#snippet asChild(props)}
            <Action {...props()} tooltip={m["events.tooltips.color"]()}>
                <Circle />
            </Action>
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
                        ...visitOptions,
                        ...optimistic.edit(event.id),
                        href: update(event.id),
                        data: { color: color == "transparent" ? null : color },
                        showProgress: false
                    }))}
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
