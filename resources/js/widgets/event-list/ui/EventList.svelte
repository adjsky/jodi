<script lang="ts">
    import { CalendarClock } from "@lucide/svelte";
    import { Event } from "$/entities/event";
    import { m } from "$/paraglide/messages";
    import { formatToHHMM } from "$/shared/lib/date";
    import { tw } from "$/shared/lib/styles";

    import { editView } from "../model/view";
    import EditEvent from "./EditEvent.svelte";

    import type { SvelteHTMLElements } from "svelte/elements";

    type Props = SvelteHTMLElements["section"] & {
        events: App.Data.EventDto[];
    };

    const { events, ...rest }: Props = $props();
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
            {#each events as event (event.id)}
                <Event.Row
                    onclick={() => editView.push(event)}
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
                        <span
                            class="table w-full table-fixed text-start font-medium"
                        >
                            <span
                                class="table-cell overflow-hidden text-ellipsis whitespace-nowrap"
                            >
                                {event.title}
                            </span>
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
                    void editView.back();
                }
            }
        }
        event={editView.isOpen() ? editView.meta : null}
    />
</section>
