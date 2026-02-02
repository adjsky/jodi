<script lang="ts">
    import { DateFormatter } from "@internationalized/date";
    import { Clock } from "@lucide/svelte";
    import { m } from "$/paraglide/messages";
    import { getLocale } from "$/paraglide/runtime";
    import { tw } from "$/shared/lib/styles";
    import { boolAttr } from "runed";

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
</script>

<div class="flex items-center justify-between">
    <div class="flex items-center gap-3">
        <!-- DO NOT REMOVE DIV -->
        <div>
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
        </div>
        {@render category()}
    </div>
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
    class="mt-3 form-input field-sizing-content w-full grow overflow-y-scroll border-none bg-transparent p-0 text-lg font-semibold text-cream-950 placeholder:text-cream-600 focus:ring-0"
    defaultValue={description ?? ""}
></textarea>

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
