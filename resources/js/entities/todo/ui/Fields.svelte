<script lang="ts">
    import { DateFormatter } from "@internationalized/date";
    import { Clock } from "@lucide/svelte";
    import { m } from "$/paraglide/messages";
    import { getLocale } from "$/paraglide/runtime";
    import { getRandomInt } from "$/shared/lib/random/get-random-int";
    import { tw } from "$/shared/lib/styles/tw";

    import type { ZonedDateTime } from "@internationalized/date";
    import type { Snippet } from "svelte";
    import type { HTMLButtonAttributes } from "svelte/elements";

    type Props = {
        scheduledAt: ZonedDateTime;
        title?: string;
        description?: string | null;
        isCompleted?: boolean;
        calendar: Snippet<[Snippet<[HTMLButtonAttributes]>]>;
        close: Snippet;
        category: Snippet;
        checkbox?: Snippet;
        time: Snippet;
        destroy: Snippet;
        repeat: Snippet;
        color: Snippet;
        notify: Snippet;
        more: Snippet;
    };

    let {
        scheduledAt,
        title,
        description,
        isCompleted,
        calendar,
        close,
        category,
        checkbox,
        time,
        destroy,
        repeat,
        color,
        notify,
        more
    }: Props = $props();

    const titleSuggestions = [
        m["todos.placeholders.title.reply-later"](),
        m["todos.placeholders.title.important-email"](),
        m["todos.placeholders.title.laundry-chair"](),
        m["todos.placeholders.title.drink-water"](),
        m["todos.placeholders.title.tiny-task"](),
        m["todos.placeholders.title.five-minute-task"](),
        m["todos.placeholders.title.scary-phone-call"](),
        m["todos.placeholders.title.buy-groceries"](),
        m["todos.placeholders.title.clean-desktop"](),
        m["todos.placeholders.title.open-document"](),
        m["todos.placeholders.title.deadline"](),
        m["todos.placeholders.title.laundry"](),
        m["todos.placeholders.title.take-vitamins"](),
        m["todos.placeholders.title.water-plants"](),
        m["todos.placeholders.title.change-sheets"](),
        m["todos.placeholders.title.book-appointment"](),
        m["todos.placeholders.title.send-reminder"](),
        m["todos.placeholders.title.return-package"](),
        m["todos.placeholders.title.clear-inbox"](),
        m["todos.placeholders.title.update-password"](),
        m["todos.placeholders.title.backup-files"](),
        m["todos.placeholders.title.meal-prep"](),
        m["todos.placeholders.title.stretch"](),
        m["todos.placeholders.title.go-outside"](),
        m["todos.placeholders.title.declutter"](),
        m["todos.placeholders.title.one-thing"](),
        m["todos.placeholders.title.stop-procrastinating"](),
        m["todos.placeholders.title.remember-why"]()
    ];

    const titlePlaceholder =
        titleSuggestions[getRandomInt(0, titleSuggestions.length - 1)];
</script>

<div class="grid grid-cols-[auto_1fr_auto] items-center gap-3">
    {#snippet trigger(props: HTMLButtonAttributes)}
        <button {...props} class="text-lg font-bold" type="button">
            {new DateFormatter(getLocale(), {
                day: "2-digit",
                year: "numeric",
                month: "short",
                weekday: "short"
            }).format(scheduledAt.toDate())}
        </button>
    {/snippet}
    {@render calendar(trigger)}
    <div class="w-full min-w-0">{@render category()}</div>
    {@render close()}
</div>

{#if checkbox}
    <div class={["mt-5 flex items-center gap-2", isCompleted && "opacity-40"]}>
        {@render checkbox()}
        {@render titleInput()}
    </div>
{:else}
    {@render titleInput("mt-5")}
{/if}

<div class="mt-4 flex items-center gap-2">
    <Clock class="text-2xl" />
    {@render time()}
</div>

<textarea
    name="description"
    placeholder={m["todos.placeholders.description"]()}
    class="mt-3 form-input w-full grow resize-none overflow-y-scroll border-none bg-transparent p-0 text-lg font-semibold text-cream-950 placeholder:text-cream-600 focus:ring-0"
    defaultValue={description ?? ""}
    maxlength={2000}
></textarea>

<div
    class="absolute inset-x-0 bottom-0 flex items-end justify-between rounded-t-2xl bg-white px-4 pb-safe-offset-6"
>
    {@render destroy()}
    {@render repeat()}
    {@render color()}
    {@render notify()}
    {@render more()}
</div>

{#snippet titleInput(classname?: string)}
    <input
        name="title"
        class={tw(
            "form-input w-full border-none bg-transparent p-0 text-xl font-bold text-cream-950 placeholder:text-cream-600 focus:ring-0",
            classname
        )}
        placeholder={titlePlaceholder}
        defaultValue={title ?? ""}
        maxlength={120}
        required
    />
{/snippet}
