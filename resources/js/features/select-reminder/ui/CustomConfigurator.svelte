<script lang="ts">
    import { Check } from "@lucide/svelte";
    import { m } from "$/paraglide/messages";
    import PromptDialog from "$/shared/ui/PromptDialog.svelte";

    import type { ZonedDateTime } from "@internationalized/date";

    const MIN_AMOUNT = 0;

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
            label: m["reminders.custom.duration.minutes"]({ a: amount }),
            template: "PT{A}M"
        },
        {
            label: m["reminders.custom.duration.hours"]({ a: amount }),
            template: "PT{A}H"
        },
        {
            label: m["reminders.custom.duration.days"]({ a: amount }),
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

    function setter(v: string) {
        const tv = v.trim();

        if (tv == "") {
            amount = tv;
            return;
        }

        const ntv = Number(tv);

        if (isNaN(ntv) || tv.length > 3 || tv.includes(".")) {
            return;
        }

        if (ntv < MIN_AMOUNT) {
            amount = MIN_AMOUNT.toString();
        } else {
            amount = tv;
        }
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
    trapFocus={false}
    onConfirm={() => {
        onSelect?.(durations[selectedIdx].template.replace("{A}", amount));
        open = false;
    }}
    onExitComplete={() => {
        [selectedIdx, amount] = getNotifyOffset();
    }}
>
    <input
        bind:value={() => amount, setter}
        inputMode="numeric"
        class="mt-4 form-input h-13.75 w-full rounded-xl border-none bg-cream-500/10 px-4 text-lg font-medium outline-none placeholder:text-cream-600 focus:ring-0"
        autocomplete="off"
    />

    <div class="mt-3">
        {#each durations as { label, template }, idx (template)}
            <button
                class="flex w-full gap-2 py-2.5"
                onclick={() => (selectedIdx = idx)}
            >
                <span
                    class={[
                        "flex size-5.5 items-center justify-center rounded-full border border-cream-950",
                        selectedIdx == idx && "bg-cream-950"
                    ]}
                >
                    {#if selectedIdx == idx}
                        <Check class="shrink-0 text-md text-white" />
                    {/if}
                </span>
                <span class="font-medium">{label}</span>
            </button>
        {/each}
    </div>
</PromptDialog>
