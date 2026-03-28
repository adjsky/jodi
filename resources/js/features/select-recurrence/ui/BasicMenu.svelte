<script lang="ts">
    import { Menu, Portal } from "@ark-ui/svelte";
    import { Check, RefreshCcw } from "@lucide/svelte";
    import { m } from "$/paraglide/messages";
    import ToolbarAction from "$/shared/ui/ToolbarAction.svelte";
    import { boolAttr } from "runed";

    const NEVER_TRIGGER_VALUE = "__never_trigger";
    const CUSTOM_TRIGGER_VALUE = "__custom_trigger";

    type Props = {
        open: boolean;
        tooltip: string;
        rrule?: string | null;
        onSelect?: (rrule: string | null) => void;
        onCustomTrigger?: VoidFunction;
    };

    let {
        open = $bindable(),
        tooltip,
        rrule,
        onSelect,
        onCustomTrigger
    }: Props = $props();

    const recurrences = [
        { label: m["recurrence.never"](), rule: NEVER_TRIGGER_VALUE },
        {
            label: m["recurrence.day"](),
            rule: "RRULE:FREQ=DAILY;INTERVAL=1"
        },
        {
            label: m["recurrence.week"](),
            rule: "RRULE:FREQ=WEEKLY;INTERVAL=1"
        },
        {
            label: m["recurrence.month"](),
            rule: "RRULE:FREQ=MONTHLY;INTERVAL=1"
        },
        {
            label: m["recurrence.year"](),
            rule: "RRULE:FREQ=YEARLY;INTERVAL=1"
        },
        {
            label: m["recurrence.custom.trigger"](),
            rule: CUSTOM_TRIGGER_VALUE
        }
    ];

    const selectedIdx = $derived(
        recurrences.findIndex(({ rule }) => {
            if (!rrule && rule == NEVER_TRIGGER_VALUE) {
                return true;
            }

            return rrule == rule;
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
            onSelect?.(value == NEVER_TRIGGER_VALUE ? null : value);
        }
    }}
>
    <Menu.Trigger>
        {#snippet asChild(props)}
            <ToolbarAction {...props()} {tooltip} class="relative">
                {#if rrule}
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="lucide lucide-refresh-ccw-icon lucide-refresh-ccw-dot"
                    >
                        <path
                            d="M13.04 3.06A9 9 0 0 0 12 3a9.75 9.75 0 0 0-6.74 2.74L3 8"
                        />
                        <path d="M3 3v5h5" />
                        <path
                            d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"
                        />
                        <path d="M16 16h5v5" />
                        <circle cx="18" cy="8" r="3"></circle>
                    </svg>
                {:else}
                    <RefreshCcw />
                {/if}
            </ToolbarAction>
        {/snippet}
    </Menu.Trigger>
    <Portal>
        <Menu.Positioner>
            <Menu.Content
                class="z-200 min-w-50 rounded-xl bg-white text-lg font-medium outline outline-cream-950"
            >
                {#each recurrences as { label, rule }, idx (rule)}
                    <Menu.Item
                        value={rule}
                        class="group cursor-pointer px-3"
                        data-selected={boolAttr(
                            rule == CUSTOM_TRIGGER_VALUE
                                ? selectedIdx == -1
                                : selectedIdx == idx
                        )}
                    >
                        <div
                            class="flex items-center justify-between border-b border-cream-300 py-3.5"
                        >
                            <span>{label}</span>
                            <Check
                                class="text-xl not-group-data-selected:hidden"
                            />
                        </div>
                    </Menu.Item>
                {/each}
            </Menu.Content>
        </Menu.Positioner>
    </Portal>
</Menu.Root>
