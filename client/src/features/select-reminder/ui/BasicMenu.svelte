<script lang="ts">
    import { Menu } from "@ark-ui/svelte";
    import { Bell, BellDot, Check } from "@lucide/svelte";
    import { m } from "#paraglide/messages.js";
    import AppPortal from "#shared/ui/AppPortal.svelte";
    import ToolbarAction from "#shared/ui/ToolbarAction.svelte";
    import { boolAttr } from "runed";

    const CUSTOM_TRIGGER_VALUE = "__custom_trigger";

    type Props = {
        open: boolean;
        tooltip: string;
        current: string;
        disabled: boolean;
        on_select?: (duration: string) => void;
        on_custom_trigger?: VoidFunction;
    };

    let {
        open = $bindable(),
        tooltip,
        current,
        disabled,
        on_select,
        on_custom_trigger
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

    const selected_idx = $derived(reminders.findIndex(({ duration }) => duration === current));
</script>

<Menu.Root
    bind:open
    positioning={{ placement: "top" }}
    onSelect={({ value }) => {
        if (value === CUSTOM_TRIGGER_VALUE) {
            on_custom_trigger?.();
        } else {
            on_select?.(value);
        }
    }}
>
    <Menu.Trigger>
        {#snippet asChild(props)}
            <ToolbarAction {...props()} {tooltip}>
                {#if !disabled}
                    <BellDot />
                {:else}
                    <Bell />
                {/if}
            </ToolbarAction>
        {/snippet}
    </Menu.Trigger>
    <AppPortal>
        <Menu.Positioner>
            <Menu.Content
                class="z-[calc(100+var(--layer-index,0))] min-w-30 rounded-xl bg-white text-lg font-medium outline outline-cream-950"
            >
                {#each reminders as { label, duration }, idx (duration)}
                    <Menu.Item
                        value={duration}
                        class="group cursor-pointer px-3"
                        data-selected={boolAttr(
                            duration === CUSTOM_TRIGGER_VALUE
                                ? selected_idx === -1
                                : selected_idx === idx
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
    </AppPortal>
</Menu.Root>
