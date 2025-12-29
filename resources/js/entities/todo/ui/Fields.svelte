<script lang="ts">
    import { Field } from "@ark-ui/svelte";
    import { DateFormatter } from "@internationalized/date";
    import { m } from "$/paraglide/messages";
    import { getLocale } from "$/paraglide/runtime";
    import { TIMEZONE } from "$/shared/cfg/constants";
    import { tw } from "$/shared/lib/styles";
    import { boolAttr } from "runed";

    import type { CalendarDate } from "@internationalized/date";
    import type { Snippet } from "svelte";

    type Props = {
        date: CalendarDate;
        title?: string;
        description?: string | null;
        isCompleted?: boolean;
        close: Snippet;
        category: Snippet;
        checkbox?: Snippet;
        destroy: Snippet;
        repeat: Snippet;
        color: Snippet;
        notify: Snippet;
        more: Snippet;
        onCalendarOpen: VoidFunction;
    };

    const {
        date,
        title,
        description,
        isCompleted,
        close,
        category,
        checkbox,
        destroy,
        repeat,
        color,
        notify,
        more,
        onCalendarOpen
    }: Props = $props();
</script>

<input name="todoDate" value={date.toString()} hidden />

<div class="flex items-center justify-between">
    <div class="flex items-center gap-3">
        <button
            class="text-lg font-bold"
            onclick={onCalendarOpen}
            type="button"
        >
            {new DateFormatter(getLocale(), {
                day: "2-digit",
                year: "numeric",
                month: "short",
                weekday: "short"
            }).format(date.toDate(TIMEZONE))}
        </button>
        {@render category()}
    </div>
    {@render close()}
</div>

{#if checkbox}
    <div class={["mt-4 flex items-center gap-2", isCompleted && "opacity-40"]}>
        {@render checkbox()}
        {@render titleInput()}
    </div>
{:else}
    {@render titleInput("mt-5")}
{/if}

<Field.Textarea
    autoresize
    name="description"
    placeholder={m["todos.placeholders.description"]()}
    class="mt-6 form-input field-sizing-content w-full border-none bg-transparent p-0 text-lg font-semibold text-cream-950 placeholder:text-cream-600 focus:ring-0"
    defaultValue={description ?? ""}
/>

<div
    class="absolute inset-x-0 bottom-0 z-10 flex items-end justify-between bg-white px-4 pb-6"
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
        placeholder={m["todos.placeholders.title"]()}
        defaultValue={title ?? ""}
        data-autofocus={boolAttr(!title)}
        required
    />
{/snippet}
