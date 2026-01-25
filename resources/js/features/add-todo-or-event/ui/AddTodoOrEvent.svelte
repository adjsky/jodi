<script lang="ts">
    import { Dialog, Portal } from "@ark-ui/svelte";
    import { parseDate, today } from "@internationalized/date";
    import { CalendarClock, Check, X } from "@lucide/svelte";
    import { YearCalendarDialog } from "$/features/filter-by-date";
    import { m } from "$/paraglide/messages";
    import { TIMEZONE } from "$/shared/cfg/constants";
    import { HistoryView } from "$/shared/inertia/history-view.svelte";
    import { useSearchParams } from "$/shared/inertia/use-search-params.svelte";
    import Sheet from "$/shared/ui/Sheet.svelte";

    import ActionButton from "./ActionButton.svelte";
    import ActionRow from "./ActionRow.svelte";
    import EventForm from "./EventForm.svelte";
    import TodoForm from "./TodoForm.svelte";

    let isCalendarOpen = $state(false);

    const view = new HistoryView();
    const searchParams = useSearchParams({ showProgress: true });

    const day = $derived(
        searchParams["d"] ? parseDate(searchParams["d"]) : today(TIMEZONE)
    );

    function onCalendarOpen() {
        isCalendarOpen = true;
    }

    function onClose() {
        view.back();
        isCalendarOpen = false;
    }
</script>

<svelte:window onpopstate={() => (isCalendarOpen = false)} />

<Dialog.Root
    bind:open={
        () => view.isOpen("add"),
        (v) => {
            if (v) {
                void view.push("add");
            } else {
                onClose();
            }
        }
    }
>
    <Dialog.Trigger>
        {#snippet asChild(props)}
            <ActionButton
                {...props()}
                class="group pointer-events-auto fixed right-4 bottom-4 z-20 rounded-2xl transition-[border-radius] data-[state=open]:rounded-[50%]"
            >
                <X
                    class="transition-transform group-data-[state=closed]:rotate-45"
                />
            </ActionButton>
        {/snippet}
    </Dialog.Trigger>
    <Portal>
        <Dialog.Backdrop
            class={[
                "fixed inset-0 z-15 bg-cream-950/60 duration-300",
                "data-[state=closed]:animate-out data-[state=closed]:fade-out",
                "data-[state=open]:animate-in data-[state=open]:fade-in"
            ]}
        />
        <Dialog.Positioner>
            <Dialog.Content
                class={[
                    "fixed right-4 bottom-23 z-20 flex flex-col items-end gap-5",
                    "data-[state=closed]:animate-out data-[state=closed]:fade-out data-[state=closed]:slide-out-to-right",
                    "data-[state=open]:animate-in data-[state=open]:fade-in data-[state=open]:slide-in-from-right"
                ]}
            >
                <ActionRow
                    title={m["events.add"]()}
                    onclick={() => view.replace("add-event")}
                >
                    <CalendarClock />
                </ActionRow>
                <ActionRow
                    title={m["todos.add"]()}
                    onclick={() => view.replace("add-todo")}
                >
                    <Check />
                </ActionRow>
            </Dialog.Content>
        </Dialog.Positioner>
    </Portal>
</Dialog.Root>

<Sheet
    bind:open={
        () => view.isOpen("add-todo") || view.isOpen("add-event"),
        (v) => {
            if (!v) {
                onClose();
            }
        }
    }
    defaultSnapPoint={0.95}
    snapPoints={[0.6, 0.95]}
    background="var(--color-white)"
    grip="var(--color-cream-300)"
>
    {#if view.isOpen("add-todo")}
        <TodoForm {day} {onCalendarOpen} {onClose} />
    {:else if view.isOpen("add-event")}
        <EventForm {day} {onCalendarOpen} {onClose} />
    {/if}

    <YearCalendarDialog
        bind:open={isCalendarOpen}
        selected={day}
        onSelect={async (date) => {
            await searchParams.update(
                { d: date.toString() },
                { only: ["todos", "events", "search"] }
            );
            isCalendarOpen = false;
        }}
    />
</Sheet>
