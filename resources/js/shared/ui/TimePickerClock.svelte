<script lang="ts">
    import { Dialog, Portal } from "@ark-ui/svelte";
    import { now, toTime } from "@internationalized/date";
    import { m } from "$/paraglide/messages";
    import { boolAttr } from "runed";

    import { TIMEZONE } from "../cfg/constants";

    import type { Time } from "@internationalized/date";

    const RADIUS = 105;

    type Props = {
        value?: Time;
        open: boolean;
        onComplete?: (time: Time) => void;
    };

    let {
        value = toTime(now(TIMEZONE)),
        open = $bindable(),
        onComplete
    }: Props = $props();

    let clockRef = $state<Element | null>(null);
    let isDragging = $state(false);
    let view = $state<"hour" | "minutes">("hour");

    let internalValue = $derived(value);
    let handlePosition = $derived(
        view === "hour"
            ? hourPosition(internalValue.hour)
            : minutePosition(internalValue.minute)
    );
    let handleAngle = $derived(
        view === "hour"
            ? (internalValue.hour % 12) * 30 - 90
            : internalValue.minute * 6 - 90
    );

    function onpointerdown(e: PointerEvent) {
        e.preventDefault();
        isDragging = true;
        handlePointer(e);
    }

    function onpointerup(e: PointerEvent) {
        if (isDragging) {
            e.preventDefault();
            isDragging = false;
            if (view == "hour") view = "minutes";
        }
    }

    function onpointermove(e: PointerEvent) {
        if (isDragging) {
            e.preventDefault();
            handlePointer(e);
        }
    }

    function hourPosition(n: number) {
        const radius = n >= 12 ? RADIUS * 0.7 : RADIUS;
        const angle = ((n % 12) * (360 / 12) - 90) * (Math.PI / 180);

        const x = Math.cos(angle) * radius;
        const y = Math.sin(angle) * radius;

        return { x, y };
    }

    function minutePosition(n: number) {
        const angle = (n * (360 / 60) - 90) * (Math.PI / 180);

        const x = Math.cos(angle) * RADIUS;
        const y = Math.sin(angle) * RADIUS;

        return { x, y };
    }

    function handlePointer(e: PointerEvent) {
        if (!clockRef) return;
        if (!e.clientX || !e.clientY) return;

        const rect = clockRef.getBoundingClientRect();
        const centerX = rect.left + rect.width / 2;
        const centerY = rect.top + rect.height / 2;
        const x = e.clientX - centerX;
        const y = e.clientY - centerY;

        let angle = Math.atan2(y, x) * (180 / Math.PI);
        angle = (angle + 90 + 360) % 360;

        if (view == "hour") {
            const distance = Math.sqrt(x * x + y * y);
            const isInnerCircle = distance < RADIUS * 0.85;

            const baseHour = Math.round(angle / 30) % 12;

            internalValue = internalValue.set({
                hour: isInnerCircle
                    ? baseHour === 0
                        ? 12
                        : baseHour + 12
                    : baseHour
            });
        } else {
            internalValue = internalValue.set({
                minute: Math.round(angle / 6) % 60
            });
        }
    }
</script>

<svelte:window {onpointermove} {onpointerup} />

<Dialog.Root
    bind:open
    onOpenChange={(details) => {
        if (!details.open) {
            internalValue = value;
        }
    }}
>
    <Portal>
        <Dialog.Backdrop
            class={[
                "fixed inset-0 z-150 bg-cream-950/60 duration-300",
                "data-[state=closed]:animate-out data-[state=closed]:fade-out",
                "data-[state=open]:animate-in data-[state=open]:fade-in"
            ]}
        />
        <Dialog.Positioner>
            <Dialog.Content
                class={[
                    "fixed top-1/2 left-1/2 z-150 w-80 -translate-1/2 rounded-4xl bg-white px-6 py-4 duration-300",
                    "data-[state=closed]:animate-out data-[state=closed]:fade-out data-[state=closed]:slide-out-to-bottom",
                    "data-[state=open]:animate-in data-[state=open]:fade-in data-[state=open]:slide-in-from-bottom"
                ]}
            >
                <Dialog.Title class="text-sm font-semibold">
                    {m["common.time-picker.title"]()}
                </Dialog.Title>
                <div
                    class="mt-4 flex items-center justify-center gap-2 text-5xl"
                >
                    <button
                        class="h-20 w-24 rounded-lg bg-cream-50 data-selected:bg-brand data-selected:text-white"
                        data-selected={boolAttr(view == "hour")}
                        onclick={() => (view = "hour")}
                    >
                        {internalValue.hour.toString().padStart(2, "0")}
                    </button>
                    :
                    <button
                        class="h-20 w-24 rounded-lg bg-cream-50 data-selected:bg-brand data-selected:text-white"
                        data-selected={boolAttr(view == "minutes")}
                        onclick={() => (view = "minutes")}
                    >
                        {internalValue.minute.toString().padStart(2, "0")}
                    </button>
                </div>
                <div
                    bind:this={clockRef}
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
                            handlePosition.x ** 2 + handlePosition.y ** 2
                        )}px; height: 2px; margin-top: -1px; transform: rotate({handleAngle}deg);"
                    ></div>
                    <div
                        aria-hidden="true"
                        class="absolute size-10 rounded-full bg-brand"
                        style="left: calc(50% + {handlePosition.x}px - 20px); top: calc(50% + {handlePosition.y}px - 20px);"
                    ></div>
                    <div role="listbox">
                        {#if view == "hour"}
                            {@render clockHH()}
                        {:else}
                            {@render clockMM()}
                        {/if}
                    </div>
                </div>
                <div class="mt-5 flex justify-end gap-8">
                    <Dialog.CloseTrigger class="text-ms font-bold text-brand">
                        {m["common.time-picker.cancel"]()}
                    </Dialog.CloseTrigger>
                    <button
                        class="text-ms font-bold text-brand"
                        onclick={() => {
                            open = false;
                            onComplete?.(internalValue);
                        }}
                    >
                        {m["common.time-picker.ok"]()}
                    </button>
                </div>
            </Dialog.Content>
        </Dialog.Positioner>
    </Portal>
</Dialog.Root>

{#snippet clockHH()}
    {#each Array.from({ length: 24 }, (_, i) => i) as hour (hour)}
        {@const { x, y } = hourPosition(hour)}
        {@const isSelected = internalValue.hour == hour}
        <span
            class="absolute flex h-8 w-8 cursor-pointer items-center justify-center font-medium data-selected:font-semibold data-selected:text-white"
            style="left: calc(50% + {x}px - 16px); top: calc(50% + {y}px - 16px);"
            role="option"
            aria-selected={isSelected}
            data-selected={boolAttr(isSelected)}
            tabindex="0"
        >
            {hour.toString().padStart(2, "0")}
        </span>
    {/each}
{/snippet}

{#snippet clockMM()}
    {#each Array.from({ length: 12 }, (_, i) => i * 5) as minute (minute)}
        {@const { x, y } = minutePosition(minute)}
        {@const isSelected = internalValue.minute == minute}
        <span
            class="absolute flex h-8 w-8 cursor-pointer items-center justify-center font-medium data-selected:font-semibold data-selected:text-white"
            style="left: calc(50% + {x}px - 16px); top: calc(50% + {y}px - 16px);"
            role="option"
            aria-selected={isSelected}
            data-selected={boolAttr(isSelected)}
            tabindex="0"
        >
            {minute.toString().padStart(2, "0")}
        </span>
    {/each}
{/snippet}
