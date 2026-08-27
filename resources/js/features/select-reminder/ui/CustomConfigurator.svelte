<script lang="ts">
    import { page } from "@inertiajs/svelte";
    import { m } from "$/paraglide/messages";
    import { VirtualKeyboard } from "$/shared/services/virtual-keyboard";
    import Button from "$/shared/ui/Button.svelte";
    import Checkbox from "$/shared/ui/Checkbox.svelte";
    import NumericInput from "$/shared/ui/NumericInput.svelte";
    import SheetDialog from "$/shared/ui/SheetDialog.svelte";

    import type { ZonedDateTime } from "@internationalized/date";

    type Props = {
        open: boolean;
        notifyAt: ZonedDateTime | null;
        startsAt: ZonedDateTime;
        onSelect?: (duration: string) => void;
    };

    let { open = $bindable(), notifyAt, startsAt, onSelect }: Props = $props();

    let [selectedIdx, amount] = $derived(getNotifyOffset());

    const window = $derived($page.props.config.reminders.window);

    const durations = $derived([
        {
            label: m["common.intervals.minutes"]({ a: amount }),
            template: "PT{A}M",
            max: window.minutes
        },
        {
            label: m["common.intervals.hours"]({ a: amount }),
            template: "PT{A}H",
            max: window.hours
        },
        {
            label: m["common.intervals.days"]({ a: amount }),
            template: "P{A}D",
            max: window.days
        }
    ]);

    function getNotifyOffset(): [number, string] {
        if (!notifyAt) {
            return [0, "10"];
        }

        const diffMs =
            startsAt.toDate().getTime() - notifyAt.toDate().getTime();
        const diffM = diffMs / (1000 * 60);
        const diffH = diffM / 60;
        const diffD = diffH / 24;

        if (diffD >= 1) return [2, Math.round(diffD).toString()];
        if (diffH >= 1) return [1, Math.round(diffH).toString()];

        return [0, Math.round(diffM).toString()];
    }
</script>

<SheetDialog
    {@attach VirtualKeyboard.retainFocus()}
    bind:open
    class="pb-0"
    height={80}
    title={m["reminders.custom.title"]()}
    onExitComplete={() => {
        [selectedIdx, amount] = getNotifyOffset();
    }}
    portal
    lazyMount
>
    <div class="flex min-h-0 flex-col overflow-y-auto overscroll-contain">
        <NumericInput
            bind:value={amount}
            class="mt-4"
            min={0}
            max={durations[selectedIdx].max}
        />

        <div class="mt-0.5">
            {#each durations as { label, template }, idx (template)}
                <Checkbox
                    {label}
                    checked={selectedIdx == idx}
                    onclick={() => (selectedIdx = idx)}
                />
            {/each}
        </div>

        <hr class="mt-4 h-px text-cream-300" />

        <div class="mt-4">
            <Checkbox
                class="opacity-60"
                label={m["reminders.custom.as-push"]()}
                checked
                disabled
            />
            <Checkbox
                class="opacity-60"
                label={m["reminders.custom.as-email"]()}
                disabled
            />
        </div>
    </div>

    <div class="mt-4 mb-safe-offset-5 flex grow items-end safe-area-keyboard">
        <Button
            type="button"
            disabled={Number(amount) === 0}
            onclick={() => {
                onSelect?.(
                    durations[selectedIdx].template.replace("{A}", amount)
                );
                open = false;
            }}
        >
            {m["reminders.custom.apply"]()}
        </Button>
    </div>
</SheetDialog>
