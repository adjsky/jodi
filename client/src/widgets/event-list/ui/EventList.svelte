<script lang="ts">
    import { parseAbsoluteToLocal, parseDate, today } from "@internationalized/date";
    import { CalendarClock } from "@lucide/svelte";
    import { Event } from "$/entities/event";
    import { m } from "$/paraglide/messages";
    import { TIMEZONE } from "$/shared/cfg/constants";
    import { useSearchParams } from "$/shared/integrations/inertia";
    import { format_to_hhmm } from "$/shared/lib/date/format-to-hh-mm";
    import { tw } from "$/shared/lib/styles/tw";

    import { get_event_day_position } from "../helpers/get-event-day-position";
    import { id } from "../helpers/id";
    import { edit_view } from "../model/view";
    import EditSheet from "./EditSheet.svelte";

    import type { EventData } from "$/entities/event";
    import type { SvelteHTMLElements } from "svelte/elements";

    type Props = SvelteHTMLElements["section"] & {
        events: EventData[];
    };

    const { events, ...rest }: Props = $props();

    const search_params = useSearchParams();

    const selected_date = $derived(
        search_params["d"] ? parseDate(search_params["d"]) : today(TIMEZONE)
    );

    const event = $derived.by(() => {
        const vid = edit_view.meta?._event?.id;
        if (!vid) return null;

        const event = events.find((e) => id(e) === vid);
        if (!event) return null;

        return event;
    });

    $effect(() => {
        if (search_params["target"] !== "event") return;

        const sid = search_params["id"];
        if (!sid || isNaN(Number(sid))) return;

        const event = events.find((t) => t.id === Number(sid));
        if (!event) return;

        void edit_view.replace({
            meta: { _event: { id: id(event) } },
            search: { d: search_params["d"] }
        });
    });
</script>

<EditSheet bind:open={() => edit_view.isOpen(), () => edit_view.back()} {event} />

<section {...rest} class={tw("px-4", rest.class)}>
    <div class="flex items-center gap-1.5">
        <CalendarClock class="text-3xl" />
        <h3 class="text-lg font-bold">{m["events.title"]()}</h3>
    </div>

    <div class="mt-4 space-y-2">
        {#if events.length === 0}
            <p class="mx-auto text-center font-medium text-cream-500">
                {m["events.no-events"]()}
            </p>
        {:else}
            {#each events as event (id(event))}
                {@const { day, total } = get_event_day_position(
                    parseAbsoluteToLocal(event.startsAt),
                    parseAbsoluteToLocal(event.endsAt),
                    selected_date
                )}
                <Event.Row
                    onclick={() => edit_view.push({ meta: { _event: { id: id(event) } } })}
                    color={event.color}
                    disabled={edit_view.isOpen()}
                >
                    {#snippet time()}
                        {#if day === 1}
                            <time
                                datetime={event?.startsAt}
                                class="text-xl font-bold text-cream-950"
                            >
                                {format_to_hhmm(new Date(event.startsAt))}
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
</section>
