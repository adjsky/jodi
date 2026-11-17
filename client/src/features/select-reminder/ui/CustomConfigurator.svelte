<script lang="ts">
    import { parseDuration } from "@internationalized/date";
    import { m } from "#paraglide/messages.js";
    import { VirtualKeyboard } from "#shared/capabilities/virtual-keyboard/mod.ts";
    import Button from "#shared/ui/Button.svelte";
    import Checkbox from "#shared/ui/Checkbox.svelte";
    import NumericInput from "#shared/ui/NumericInput.svelte";
    import SheetDialog from "#shared/ui/SheetDialog.svelte";

    type Props = {
        open: boolean;
        current: string;
        on_select?: (duration: string) => void;
    };

    let { open = $bindable(), current, on_select }: Props = $props();

    let [selected_idx, amount] = $derived(get_notify_offset());

    const durations = $derived([
        {
            label: m["common.intervals.minutes"]({ a: amount }),
            template: "PT{A}M",
            max: 600
        },
        {
            label: m["common.intervals.hours"]({ a: amount }),
            template: "PT{A}H",
            max: 120
        },
        {
            label: m["common.intervals.days"]({ a: amount }),
            template: "P{A}D",
            max: 31
        }
    ]);

    function get_notify_offset(): [number, string] {
        const { days, hours, minutes } = parseDuration(current);

        if (days) {
            return [2, days.toString()];
        }

        if (hours) {
            return [1, hours.toString()];
        }

        return [0, minutes.toString()];
    }
</script>

<SheetDialog
    {@attach VirtualKeyboard.retain_focus()}
    bind:open
    class="pb-0"
    height={80}
    title={m["reminders.custom.title"]()}
    on_exit_complete={() => {
        [selected_idx, amount] = get_notify_offset();
    }}
    portal
    lazy_mount
>
    <div class="flex min-h-0 flex-col overflow-y-auto overscroll-contain">
        <NumericInput bind:value={amount} class="mt-4" min={0} max={durations[selected_idx].max} />

        <div class="mt-0.5">
            {#each durations as { label, template }, idx (template)}
                <Checkbox
                    {label}
                    checked={selected_idx === idx}
                    onclick={() => (selected_idx = idx)}
                />
            {/each}
        </div>

        <hr class="mt-4 h-px text-cream-300" />

        <div class="mt-4">
            <Checkbox class="opacity-60" label={m["reminders.custom.as-push"]()} checked disabled />
            <Checkbox class="opacity-60" label={m["reminders.custom.as-email"]()} disabled />
        </div>
    </div>

    <div class="mt-4 mb-safe-offset-5 flex grow items-end safe-area-keyboard">
        <Button
            type="button"
            disabled={Number(amount) === 0}
            onclick={() => {
                on_select?.(durations[selected_idx].template.replace("{A}", amount));
                open = false;
            }}
        >
            {m["reminders.custom.apply"]()}
        </Button>
    </div>
</SheetDialog>
