<script lang="ts">
    import { Menu } from "@ark-ui/svelte";
    import { parseDuration } from "@internationalized/date";
    import {
        CalendarDays,
        CalendarRange,
        Ellipsis,
        Sunrise
    } from "@lucide/svelte";
    import { m } from "$/paraglide/messages";
    import ToolbarAction from "$/shared/ui/ToolbarAction.svelte";

    import type { CalendarDate } from "@internationalized/date";

    type Props = {
        startsAt: CalendarDate;
        tooltip: string;
        onReschedule?: (date: CalendarDate) => void;
    };

    let { startsAt, tooltip, onReschedule }: Props = $props();

    const options = [
        {
            label: m["reschedule.tomorrow"](),
            duration: "P1D",
            icon: Sunrise
        },
        {
            label: m["reschedule.next-week"](),
            duration: "P1W",
            icon: CalendarDays
        },
        {
            label: m["reschedule.next-month"](),
            duration: "P1M",
            icon: CalendarRange
        }
    ];
</script>

<Menu.Root
    positioning={{ placement: "top" }}
    onSelect={({ value }) => {
        onReschedule?.(startsAt.add(parseDuration(value)));
    }}
>
    <Menu.Trigger>
        {#snippet asChild(props)}
            <ToolbarAction {...props()} {tooltip}>
                <Ellipsis />
            </ToolbarAction>
        {/snippet}
    </Menu.Trigger>
    <Menu.Positioner>
        <Menu.Content
            class="z-10 rounded-xl bg-white text-lg font-medium outline outline-cream-950"
        >
            {#each options as { label, duration, icon: Icon } (duration)}
                <Menu.Item value={duration} class="group cursor-pointer px-3">
                    <div
                        class="flex items-center justify-between gap-3 border-b border-cream-300 py-3.5"
                    >
                        <span>{label}</span>
                        <Icon />
                    </div>
                </Menu.Item>
            {/each}
        </Menu.Content>
    </Menu.Positioner>
</Menu.Root>
