<script lang="ts">
    import { m } from "$/paraglide/messages";
    import Checkbox from "$/shared/ui/Checkbox.svelte";
    import NumericInput from "$/shared/ui/NumericInput.svelte";
    import PromptDialog from "$/shared/ui/PromptDialog.svelte";

    import type { ZonedDateTime } from "@internationalized/date";

    type Props = {
        open: boolean;
        notifyAt: ZonedDateTime | null;
        startsAt: ZonedDateTime;
        onSelect?: (duration: string) => void;
    };

    let { open = $bindable(), notifyAt, startsAt, onSelect }: Props = $props();

    let [selectedIdx, amount] = $derived(getNotifyOffset());

    const durations = $derived([
        {
            label: m["common.intervals.minutes"]({ a: amount }),
            template: "PT{A}M"
        },
        {
            label: m["common.intervals.hours"]({ a: amount }),
            template: "PT{A}H"
        },
        {
            label: m["common.intervals.days"]({ a: amount }),
            template: "P{A}D"
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

<PromptDialog
    bind:open
    title={m["reminders.custom.title"]()}
    label={{
        abort: m["reminders.custom.cancel"](),
        confirm: m["reminders.custom.ok"]()
    }}
    disabled={Number(amount) === 0}
    onConfirm={() => {
        onSelect?.(durations[selectedIdx].template.replace("{A}", amount));
        open = false;
    }}
    onExitComplete={() => {
        [selectedIdx, amount] = getNotifyOffset();
    }}
>
    <NumericInput bind:value={amount} class="mt-4" min={0} />

    <div class="mt-3">
        {#each durations as { label, template }, idx (template)}
            <Checkbox
                {label}
                checked={selectedIdx == idx}
                onclick={() => (selectedIdx = idx)}
            />
        {/each}
    </div>
</PromptDialog>
