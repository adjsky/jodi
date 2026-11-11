<script lang="ts">
    import { Dialog } from "@ark-ui/svelte";
    import { CalendarClock, Check, X } from "@lucide/svelte";
    import { m } from "#paraglide/messages.js";
    import { Dismissal } from "#shared/capabilities/dismissal/mod.svelte.ts";
    import { LastMatching } from "#shared/lib/svelte/last-matching.svelte.ts";
    import AppPortal from "#shared/ui/AppPortal.svelte";
    import Sheet from "#shared/ui/Sheet.svelte";

    import ActionButton from "./ActionButton.svelte";
    import ActionRow from "./ActionRow.svelte";
    import EventForm from "./EventForm.svelte";
    import TodoForm from "./TodoForm.svelte";

    let open: "picker" | "todo" | "event" | false = $state(false);

    const lastOpen = new LastMatching(
        () => open,
        () => open !== false
    );

    Dismissal.use(
        () => open === "picker",
        () => (open = false)
    );
</script>

<Dialog.Root
    bind:open={
        () => open === "picker",
        (v) => {
            if (v) {
                open = "picker";
            } else {
                open = false;
            }
        }
    }
>
    <Dialog.Trigger>
        {#snippet asChild(props)}
            <ActionButton
                {...props()}
                class="group pointer-events-auto fixed right-4 bottom-safe-offset-4 z-20 rounded-2xl transition-[border-radius] data-[state=open]:rounded-[50%]"
            >
                <X
                    class="transition-transform group-data-[state=closed]:rotate-45"
                />
            </ActionButton>
        {/snippet}
    </Dialog.Trigger>
    <AppPortal>
        <Dialog.Backdrop
            class={[
                "fixed inset-0 z-15 bg-cream-950/60 duration-300",
                "data-[state=closed]:animate-out data-[state=closed]:fade-out",
                "data-[state=open]:animate-in data-[state=open]:fade-in"
            ]}
        />
        <Dialog.Content
            class={[
                "fixed right-4 bottom-safe-offset-23 z-20 flex flex-col items-end gap-5",
                "data-[state=closed]:animate-out data-[state=closed]:fade-out data-[state=closed]:slide-out-to-right",
                "data-[state=open]:animate-in data-[state=open]:fade-in data-[state=open]:slide-in-from-right"
            ]}
        >
            <ActionRow
                title={m["events.add"]()}
                onclick={() => (open = "event")}
            >
                <CalendarClock />
            </ActionRow>
            <ActionRow title={m["todos.add"]()} onclick={() => (open = "todo")}>
                <Check />
            </ActionRow>
        </Dialog.Content>
    </AppPortal>
</Dialog.Root>

<Sheet
    bind:open={() => open === "event" || open === "todo", () => (open = false)}
    maxHeight={0.9}
    snapPoints={[0.6, 0.9]}
    defaultSnapPoint={1}
    onExitComplete={() => {
        lastOpen.reset();
    }}
>
    {#if lastOpen.current === "todo"}
        <TodoForm onClose={() => (open = false)} />
    {:else if lastOpen.current === "event"}
        <EventForm onClose={() => (open = false)} />
    {/if}
</Sheet>
