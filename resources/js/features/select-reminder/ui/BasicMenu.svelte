<script lang="ts">
    import { Menu } from "@ark-ui/svelte";
    import { Bell, BellDot, Check } from "@lucide/svelte";
    import { m } from "$/paraglide/messages";
    import ToolbarAction from "$/shared/ui/ToolbarAction.svelte";
    import { boolAttr } from "runed";

    import { durationToZonedDT } from "../helpers/duration";

    import type { ZonedDateTime } from "@internationalized/date";

    const CUSTOM_TRIGGER_VALUE = "__custom_trigger";

    type Props = {
        open: boolean;
        tooltip: string;
        notifyAt: ZonedDateTime | null;
        startsAt: ZonedDateTime;
        onSelect?: (duration: string) => void;
        onCustomTrigger?: VoidFunction;
    };

    let {
        open = $bindable(),
        tooltip,
        notifyAt,
        startsAt,
        onSelect,
        onCustomTrigger
    }: Props = $props();

    const reminders = [
        { label: m["reminders.hour"]({ h: 1 }), duration: "PT1H" },
        { label: m["reminders.hour"]({ h: 3 }), duration: "PT3H" },
        { label: m["reminders.day"]({ d: 1 }), duration: "P1D" },
        {
            label: m["reminders.custom.trigger"](),
            duration: CUSTOM_TRIGGER_VALUE
        }
    ];

    const selectedIdx = $derived(
        reminders.findIndex(({ duration }) => {
            if (!notifyAt || duration == CUSTOM_TRIGGER_VALUE) {
                return false;
            }

            return durationToZonedDT(startsAt, duration).compare(notifyAt) == 0;
        })
    );
</script>

<Menu.Root
    bind:open
    positioning={{ placement: "top" }}
    onSelect={({ value }) => {
        if (value == CUSTOM_TRIGGER_VALUE) {
            onCustomTrigger?.();
        } else {
            onSelect?.(value);
        }
    }}
>
    <Menu.Trigger>
        {#snippet asChild(props)}
            <ToolbarAction {...props()} {tooltip}>
                {#if notifyAt}
                    <BellDot />
                {:else}
                    <Bell />
                {/if}
            </ToolbarAction>
        {/snippet}
    </Menu.Trigger>
    <Menu.Positioner>
        <Menu.Content
            class="z-10 min-w-30 rounded-xl bg-white text-lg font-medium outline outline-cream-950"
        >
            {#each reminders as { label, duration }, idx (duration)}
                <Menu.Item
                    value={duration}
                    class="group cursor-pointer px-3"
                    data-selected={boolAttr(
                        duration == CUSTOM_TRIGGER_VALUE
                            ? selectedIdx == -1
                            : selectedIdx == idx
                    )}
                >
                    <div
                        class="flex items-center justify-between border-b border-cream-300 py-3.5"
                    >
                        <span>{label}</span>
                        <Check class="text-xl not-group-data-selected:hidden" />
                    </div>
                </Menu.Item>
            {/each}
        </Menu.Content>
    </Menu.Positioner>
</Menu.Root>
