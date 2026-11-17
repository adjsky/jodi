<script lang="ts">
    import { now, toTime } from "@internationalized/date";
    import { m } from "#paraglide/messages.js";
    import { Haptics } from "#shared/capabilities/haptics/mod.ts";
    import { TIMEZONE } from "#shared/cfg/constants.ts";
    import { boolAttr } from "runed";

    import PromptDialog from "./PromptDialog.svelte";

    import type { Time } from "@internationalized/date";

    const RADIUS = 105;

    type Props = {
        value?: Time;
        open: boolean;
        on_abort?: VoidFunction;
        on_confirm?: (time: Time) => void;
    };

    let {
        value = $bindable(toTime(now(TIMEZONE))),
        open = $bindable(),
        on_abort,
        on_confirm
    }: Props = $props();

    let clock_ref = $state<Element | null>(null);
    let is_dragging = $state(false);
    let view = $state<"hour" | "minutes">("hour");

    let internal_value = $derived(value);
    let handle_position = $derived(
        view === "hour"
            ? hour_position(internal_value.hour)
            : minute_position(internal_value.minute)
    );
    let handle_angle = $derived(
        view === "hour" ? (internal_value.hour % 12) * 30 - 90 : internal_value.minute * 6 - 90
    );

    function onpointerdown(e: PointerEvent) {
        e.preventDefault();
        is_dragging = true;
        void Haptics.selection_start();
        handle_pointer(e);
    }

    function onpointerup(e: PointerEvent) {
        if (is_dragging) {
            e.preventDefault();
            is_dragging = false;
            void Haptics.selection_end();
            if (view === "hour") view = "minutes";
        }
    }

    function onpointermove(e: PointerEvent) {
        if (is_dragging) {
            e.preventDefault();
            handle_pointer(e);
        }
    }

    function hour_position(n: number) {
        const radius = n >= 12 ? RADIUS * 0.7 : RADIUS;
        const angle = ((n % 12) * (360 / 12) - 90) * (Math.PI / 180);

        const x = Math.cos(angle) * radius;
        const y = Math.sin(angle) * radius;

        return { x, y };
    }

    function minute_position(n: number) {
        const angle = (n * (360 / 60) - 90) * (Math.PI / 180);

        const x = Math.cos(angle) * RADIUS;
        const y = Math.sin(angle) * RADIUS;

        return { x, y };
    }

    function handle_pointer(e: PointerEvent) {
        if (!clock_ref) return;
        if (!e.clientX || !e.clientY) return;

        const rect = clock_ref.getBoundingClientRect();
        const center_x = rect.left + rect.width / 2;
        const center_y = rect.top + rect.height / 2;
        const x = e.clientX - center_x;
        const y = e.clientY - center_y;

        let angle = Math.atan2(y, x) * (180 / Math.PI);
        angle = (angle + 90 + 360) % 360;

        if (view === "hour") {
            const distance = Math.sqrt(x * x + y * y);
            const is_inner_circle = distance < RADIUS * 0.85;

            const base_hour = Math.round(angle / 30) % 12;
            const new_hour = is_inner_circle ? (base_hour === 0 ? 12 : base_hour + 12) : base_hour;

            if (new_hour !== internal_value.hour) {
                void Haptics.selection_changed();
            }

            internal_value = internal_value.set({ hour: new_hour });
        } else {
            const new_minute = Math.round(angle / 6) % 60;

            if (new_minute !== internal_value.minute) {
                void Haptics.selection_changed();
            }

            internal_value = internal_value.set({ minute: new_minute });
        }
    }
</script>

<svelte:window {onpointermove} {onpointerup} />

<PromptDialog
    bind:open
    {on_abort}
    title={m["time-picker.title"]()}
    label={{
        abort: m["time-picker.cancel"](),
        confirm: m["time-picker.ok"]()
    }}
    on_confirm={() => {
        open = false;
        value = internal_value;
        on_confirm?.(internal_value);
    }}
    on_exit_complete={() => {
        internal_value = value;
        view = "hour";
    }}
>
    <div class="mt-4 flex items-center justify-center gap-2 text-5xl">
        <button
            class="h-20 w-24 rounded-lg bg-cream-50 data-selected:bg-brand data-selected:text-white"
            data-selected={boolAttr(view === "hour")}
            onclick={() => (view = "hour")}
        >
            {internal_value.hour.toString().padStart(2, "0")}
        </button>
        :
        <button
            class="h-20 w-24 rounded-lg bg-cream-50 data-selected:bg-brand data-selected:text-white"
            data-selected={boolAttr(view === "minutes")}
            onclick={() => (view = "minutes")}
        >
            {internal_value.minute.toString().padStart(2, "0")}
        </button>
    </div>
    <div
        bind:this={clock_ref}
        class="relative mt-5 aspect-square w-full touch-none rounded-full bg-cream-50 select-none"
        role="group"
        {onpointerdown}
    >
        <div
            aria-hidden="true"
            class="absolute top-1/2 left-1/2 size-2 -translate-1/2 rounded-full bg-brand"
        ></div>
        <div
            aria-hidden="true"
            class="absolute top-1/2 left-1/2 origin-left bg-brand"
            style="width: {Math.sqrt(
                handle_position.x ** 2 + handle_position.y ** 2
            )}px; height: 2px; margin-top: -1px; transform: rotate({handle_angle}deg);"
        ></div>
        <div
            aria-hidden="true"
            class="absolute size-10 rounded-full bg-brand"
            style="left: calc(50% + {handle_position.x}px - 20px); top: calc(50% + {handle_position.y}px - 20px);"
        ></div>
        <div role="listbox">
            {#if view === "hour"}
                {@render clock_hh()}
            {:else}
                {@render clock_mm()}
            {/if}
        </div>
    </div>
</PromptDialog>

{#snippet clock_hh()}
    {#each Array.from({ length: 24 }, (_, i) => i) as hour (hour)}
        {@const { x, y } = hour_position(hour)}
        {@const selected = internal_value.hour === hour}
        <span
            class="absolute flex h-8 w-8 cursor-pointer items-center justify-center font-medium data-selected:font-semibold data-selected:text-white"
            style="left: calc(50% + {x}px - 16px); top: calc(50% + {y}px - 16px);"
            role="option"
            aria-selected={selected}
            data-selected={boolAttr(selected)}
            tabindex="0"
        >
            {hour.toString().padStart(2, "0")}
        </span>
    {/each}
{/snippet}

{#snippet clock_mm()}
    {#each Array.from({ length: 12 }, (_, i) => i * 5) as minute (minute)}
        {@const { x, y } = minute_position(minute)}
        {@const selected = internal_value.minute === minute}
        <span
            class="absolute flex h-8 w-8 cursor-pointer items-center justify-center font-medium data-selected:font-semibold data-selected:text-white"
            style="left: calc(50% + {x}px - 16px); top: calc(50% + {y}px - 16px);"
            role="option"
            aria-selected={selected}
            data-selected={boolAttr(selected)}
            tabindex="0"
        >
            {minute.toString().padStart(2, "0")}
        </span>
    {/each}
{/snippet}
