<script lang="ts">
    import { Time, toTime } from "@internationalized/date";
    import { Plus, X } from "@lucide/svelte";
    import { m } from "#paraglide/messages.js";
    import TimePickerInput from "#shared/ui/TimePickerInput.svelte";

    import type { ZonedDateTime } from "@internationalized/date";
    import type { HTMLButtonAttributes } from "svelte/elements";

    type Props = {
        scheduled_at: ZonedDateTime;
        has_time: boolean;
        on_change?: (time: Time, has_time: boolean) => void;
    };

    let { scheduled_at = $bindable(), has_time, on_change }: Props = $props();
</script>

<div class="relative flex h-10 items-center rounded-lg border border-cream-300 text-lg">
    {#snippet trigger(props: HTMLButtonAttributes)}
        <button {...props} class="flex h-full items-center gap-1.5 px-3 font-semibold">
            <Plus class="text-xl" />
            {m["todos.time.add"]()}
        </button>
    {/snippet}
    <TimePickerInput
        bind:value={
            () => (has_time ? toTime(scheduled_at) : new Time(12, 0)),
            (time) => (scheduled_at = scheduled_at.set(time))
        }
        class="h-full w-max pr-10 pl-3"
        trigger={has_time ? undefined : trigger}
        on_confirm={async (time) => {
            on_change?.(time, true);
        }}
    />

    {#if has_time}
        <button
            class="absolute right-1.5 p-1 font-bold"
            onclick={async (e) => {
                e.stopPropagation();

                const time = new Time(0, 0, 0, 0);
                scheduled_at.set(time);
                on_change?.(time, false);
            }}
        >
            <X class="text-xl" />
        </button>
    {/if}
</div>
