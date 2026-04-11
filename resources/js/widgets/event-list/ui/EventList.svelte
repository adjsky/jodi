<script lang="ts">
    import { CalendarClock } from "@lucide/svelte";
    import { Event } from "$/entities/event";
    import { m } from "$/paraglide/messages";
    import { useSearchParams } from "$/shared/inertia/use-search-params.svelte";
    import { formatToHHMM } from "$/shared/lib/date";
    import { tw } from "$/shared/lib/styles";

    import { id } from "../helpers/id";
    import { editView } from "../model/view";
    import EditSheet from "./EditSheet.svelte";

    import type { EventData } from "$/entities/event/model/types";
    import type { SvelteHTMLElements } from "svelte/elements";

    type Props = SvelteHTMLElements["section"] & {
        events: EventData[];
    };

    const { events, ...rest }: Props = $props();

    const searchParams = useSearchParams();

    $effect(() => {
        if (searchParams["target"] !== "event") return;

        const id = searchParams["id"];
        if (!id || isNaN(Number(id))) return;

        const event = events.find((t) => t.id === Number(id));
        if (!event) return;

        void editView.replace({
            meta: event,
            search: { d: searchParams["d"] }
        });
    });
</script>

<section {...rest} class={tw("px-4", rest.class)}>
    <div class="flex items-center gap-1.5">
        <CalendarClock class="text-3xl" />
        <h3 class="text-lg font-bold">{m["events.title"]()}</h3>
    </div>

    <div class="mt-4 space-y-2">
        {#if events.length == 0}
            <p class="mx-auto text-center font-medium text-cream-500">
                {m["events.no-events"]()}
            </p>
        {:else}
            {#each events as event (id(event))}
                <Event.Row
                    onclick={() => editView.push({ meta: event })}
                    color={event.color}
                >
                    {#snippet time()}
                        <time
                            datetime={event?.startsAt}
                            class="text-xl font-black text-brand"
                        >
                            {formatToHHMM(new Date(event.startsAt))}
                        </time>
                    {/snippet}
                    {#snippet title()}
                        <span class="truncate text-start font-medium">
                            {event.title}
                        </span>
                    {/snippet}
                </Event.Row>
            {/each}
        {/if}
    </div>

    <EditSheet
        bind:open={() => editView.isOpen(), () => editView.back()}
        event={editView.isOpen() ? editView.meta : null}
    />
</section>
