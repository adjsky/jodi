<script lang="ts">
    import { CalendarClock } from "@lucide/svelte";
    import { Event } from "$/entities/event";
    import { m } from "$/paraglide/messages";
    import { HistoryView } from "$/shared/inertia/history-view.svelte";
    import { tw } from "$/shared/lib/styles";
    import Skeleton from "$/shared/ui/Skeleton.svelte";
    import dayjs from "dayjs";
    import { isDeepEqual } from "remeda";
    import { watch } from "runed";

    import EditEvent from "./EditEvent.svelte";

    import type { SvelteHTMLElements } from "svelte/elements";

    type Props = SvelteHTMLElements["section"] & {
        loading: boolean;
        events: App.Data.EventDto[];
    };

    const { events, loading, ...rest }: Props = $props();

    const renderableEvents = $derived(
        loading
            ? (Array.from({ length: 7 }, (_, index) => ({
                  id: index
              })) as App.Data.EventDto[])
            : events
    );

    const editView = new HistoryView<App.Data.EventDto>("edit-event");

    watch(
        () => [editView.meta],
        () => {
            if (!editView.isOpen()) {
                return;
            }

            const event = events?.find((e) => e.id == editView.meta?.id);

            if (!event) {
                return;
            }

            if (!isDeepEqual(editView.meta, event)) {
                void editView.open(event);
            }
        }
    );
</script>

<section {...rest} class={tw("px-4", rest.class)}>
    <div class="flex items-center gap-1.5">
        <CalendarClock class="text-3xl" />
        <h3 class="text-lg font-bold">{m["events.title"]()}</h3>
    </div>

    <div class="mt-4 space-y-2">
        {#if renderableEvents.length == 0}
            <p class="mx-auto text-center font-medium text-cream-500">
                {m["events.no-events"]()}
            </p>
        {:else}
            {#each renderableEvents as event (event.id)}
                <Event.Row
                    disabled={loading}
                    onclick={() => editView.open(event)}
                >
                    {#snippet time()}
                        <time
                            datetime={event?.startsAt}
                            class="text-xl font-black text-brand"
                        >
                            {#if loading}
                                <Skeleton inline style="width: 60px" />
                            {:else}
                                {dayjs(event.startsAt).format("HH:mm")}
                            {/if}
                        </time>
                    {/snippet}
                    {#snippet title()}
                        <span
                            class="table w-full table-fixed text-start font-medium"
                        >
                            {#if loading}
                                <Skeleton
                                    inline
                                    style="width: {Math.random() * 100 + 100}px"
                                />
                            {:else}
                                <span
                                    class="table-cell overflow-hidden text-ellipsis whitespace-nowrap"
                                >
                                    {event.title}
                                </span>
                            {/if}
                        </span>
                    {/snippet}
                </Event.Row>
            {/each}
        {/if}
    </div>

    <EditEvent
        bind:open={
            () => editView.isOpen(),
            (v) => {
                if (!v) {
                    editView.close();
                }
            }
        }
        event={editView.meta}
    />
</section>
