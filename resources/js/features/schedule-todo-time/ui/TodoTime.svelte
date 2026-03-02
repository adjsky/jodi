<script lang="ts">
    import { Time, toTime } from "@internationalized/date";
    import { Plus, X } from "@lucide/svelte";
    import { m } from "$/paraglide/messages";
    import TimePickerInput from "$/shared/ui/TimePickerInput.svelte";

    import type { ZonedDateTime } from "@internationalized/date";
    import type { HTMLButtonAttributes } from "svelte/elements";

    type Props = {
        scheduledAt: ZonedDateTime;
        hasTime: boolean;
        onChange?: (time: Time, hasTime: boolean) => void;
    };

    let {
        scheduledAt = $bindable(),
        hasTime = $bindable(),
        onChange
    }: Props = $props();
</script>

<div
    class="relative flex h-10 items-center rounded-lg border border-cream-300 text-lg"
>
    {#snippet trigger(props: HTMLButtonAttributes)}
        <button
            {...props}
            class="flex h-full items-center gap-1.5 px-3 font-semibold"
        >
            <Plus class="text-xl" />
            {m["todos.time.add"]()}
        </button>
    {/snippet}
    <TimePickerInput
        bind:value={
            () => (hasTime ? toTime(scheduledAt) : new Time(12, 0)),
            (time) => (scheduledAt = scheduledAt.set(time))
        }
        class="h-full w-max pr-10 pl-3"
        trigger={hasTime ? undefined : trigger}
        onConfirm={async (time) => {
            if (!hasTime) {
                hasTime = true;
            }
            onChange?.(time, true);
        }}
    />

    {#if hasTime}
        <button
            class="absolute right-1.5 p-1 font-bold"
            onclick={async (e) => {
                e.stopPropagation();

                hasTime = false;
                const time = new Time(0, 0, 0, 0);
                scheduledAt.set(time);
                onChange?.(time, false);
            }}
        >
            <X class="text-xl" />
        </button>
    {/if}
</div>
