<script lang="ts">
    import { Menu } from "@ark-ui/svelte";
    import { parseDuration } from "@internationalized/date";
    import { Bell, BellDot, Check } from "@lucide/svelte";
    import { m } from "$/paraglide/messages";
    import { link } from "$/shared/inertia/link";
    import { normalizeIsoString } from "$/shared/lib/date";
    import { noop } from "$/shared/lib/function";
    import ToolbarAction from "$/shared/ui/ToolbarAction.svelte";
    import { boolAttr } from "runed";

    import type { ZonedDateTime } from "@internationalized/date";
    import type { LinkParameters } from "$/shared/inertia/link";

    type Props = LinkParameters & {
        tooltip: string;
        current: ZonedDateTime | null;
        start: ZonedDateTime;
        name?: string;
    };

    let {
        tooltip,
        current = $bindable(),
        start,
        name,
        ...options
    }: Props = $props();

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
    bind:open
    positioning={{ placement: "top" }}
    onSelect={({ value }) => {
        current = durationToZonedDT(value);
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
            class="min-w-30 rounded-xl bg-white outline outline-cream-950"
        >
            {#each reminders as { label, value } (value)}
                {@const inertia = name ? (noop as never) : link}
                <Menu.Item
                    {@attach inertia(() => ({
                        ...options,
                        data: {
                            notifyAt: normalizeIsoString(
                                durationToZonedDT(value).toAbsoluteString()
                            )
                        },
                        showProgress: false
                    }))}
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

{#if name}
    <input hidden value={current?.toAbsoluteString()} {name} />
{/if}
