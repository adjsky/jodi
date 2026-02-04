<script lang="ts">
    import { Menu } from "@ark-ui/svelte";
    import { parseDuration } from "@internationalized/date";
    import { Bell, BellDot, Check } from "@lucide/svelte";
    import { m } from "$/paraglide/messages";
    import { normalizeIsoString } from "$/shared/lib/date";
    import { announce } from "$/shared/lib/form";
    import ToolbarAction from "$/shared/ui/ToolbarAction.svelte";
    import { boolAttr } from "runed";
    import { tick } from "svelte";

    import type { ZonedDateTime } from "@internationalized/date";

    type Props = {
        tooltip: string;
        current: ZonedDateTime | null;
        start: ZonedDateTime;
        name: string;
        beforeOpen?: () => void | boolean;
    };

    let {
        tooltip,
        current = $bindable(),
        start,
        name,
        beforeOpen
    }: Props = $props();

    let announcerInput = $state<HTMLInputElement | null>(null);
    let open = $state(false);

    const reminders = [
        { label: m["reminders.hour"]({ h: 1 }), value: "PT1H" },
        { label: m["reminders.hour"]({ h: 3 }), value: "PT3H" },
        { label: m["reminders.day"]({ d: 1 }), value: "P1D" }
    ];

    function durationToZonedDT(duration: string) {
        return start.subtract(parseDuration(duration));
    }
</script>

<Menu.Root
    bind:open={
        () => open,
        (v) => {
            if (beforeOpen && beforeOpen() === false) return;
            open = v;
        }
    }
    positioning={{ placement: "top" }}
    onSelect={async ({ value }) => {
        current = durationToZonedDT(value);
        await tick();
        announce(announcerInput);
    }}
>
    <Menu.Trigger>
        {#snippet asChild(props)}
            <ToolbarAction {...props()} {tooltip}>
                {#if current}
                    <BellDot />
                {:else}
                    <Bell />
                {/if}
            </ToolbarAction>
        {/snippet}
    </Menu.Trigger>
    <Menu.Positioner>
        <Menu.Content
            class="z-10 min-w-30 rounded-xl bg-white outline outline-cream-950"
        >
            {#each reminders as { label, value } (value)}
                <Menu.Item
                    {value}
                    class="group cursor-pointer px-3 text-lg font-medium"
                    data-selected={boolAttr(
                        current
                            ? durationToZonedDT(value).compare(current) == 0
                            : false
                    )}
                >
                    <div
                        class="flex items-center justify-between border-cream-300 py-3.5 group-not-last:border-b"
                    >
                        <span>{label}</span>
                        <Check class="text-xl not-group-data-selected:hidden" />
                    </div>
                </Menu.Item>
            {/each}
        </Menu.Content>
    </Menu.Positioner>
</Menu.Root>

<input
    bind:this={announcerInput}
    hidden
    value={current ? normalizeIsoString(current.toAbsoluteString()) : null}
    {name}
/>
