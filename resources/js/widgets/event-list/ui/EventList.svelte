<script lang="ts">
    import {
        parseAbsoluteToLocal,
        parseDate,
        today
    } from "@internationalized/date";
    import { CalendarClock } from "@lucide/svelte";
    import { Event } from "$/entities/event";
    import { m } from "$/paraglide/messages";
    import { TIMEZONE } from "$/shared/cfg/constants";
    import { useSearchParams } from "$/shared/integrations/inertia";
    import { formatToHHMM } from "$/shared/lib/date/format-to-hh-mm";
    import { tw } from "$/shared/lib/styles/tw";

    import { getEventDayPosition } from "../helpers/get-event-day-position";
    import { id } from "../helpers/id";
    import { editView } from "../model/view";
    import EditSheet from "./EditSheet.svelte";

    import type { EventData } from "$/entities/event";
    import type { SvelteHTMLElements } from "svelte/elements";

    type Props = SvelteHTMLElements["section"] & {
        events: EventData[];
    };

    const { events, ...rest }: Props = $props();

    const searchParams = useSearchParams();

    const selectedDate = $derived(
        searchParams["d"] ? parseDate(searchParams["d"]) : today(TIMEZONE)
    );

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
                {@const { day, total } = getEventDayPosition(
                    parseAbsoluteToLocal(event.startsAt),
                    parseAbsoluteToLocal(event.endsAt),
                    selectedDate
                )}
                <Event.Row
                    onclick={() => editView.push({ meta: event })}
                    color={event.color}
                    disabled={editView.isOpen()}
                >
                    {#snippet time()}
                        {#if day == 1}
                            <time
                                datetime={event?.startsAt}
                                class="text-xl font-bold text-cream-950"
                            >
                                {formatToHHMM(new Date(event.startsAt))}
                            </time>
                        {:else}
                            <span class="text-xl font-bold text-cream-950">
                                {m["events.day-of"]({ day, total })}
                            </span>
                        {/if}
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
