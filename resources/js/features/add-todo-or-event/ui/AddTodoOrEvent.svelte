<script lang="ts">
    import { Dialog, Portal } from "@ark-ui/svelte";
    import { CalendarClock, Check, X } from "@lucide/svelte";
    import { m } from "$/paraglide/messages";
    import Sheet from "$/shared/ui/Sheet.svelte";

    import { view } from "../model/view";
    import ActionButton from "./ActionButton.svelte";
    import ActionRow from "./ActionRow.svelte";
    import EventForm from "./EventForm.svelte";
    import TodoForm from "./TodoForm.svelte";

    type Props = {
        loading: boolean;
    };

    const { loading }: Props = $props();
</script>

<Dialog.Root
    bind:open={
        () => view.isOpen() && view.meta == null,
        (v) => {
            if (v) {
                void view.open();
            } else {
                view.back();
            }
        }
    }
>
    <Dialog.Trigger disabled={loading}>
        {#snippet asChild(props)}
            <ActionButton
                {...props()}
                class="pointer-events-auto fixed right-4 bottom-4 z-20"
            >
                <X
                    class={[
                        !view.isOpen() && "rotate-45",
                        "transition-transform"
                    ]}
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
                    onclick={() => view.updateMeta({ entity: "event" })}
                >
                    <CalendarClock />
                </ActionRow>
                <ActionRow
                    title={m["todos.add"]()}
                    onclick={() => view.updateMeta({ entity: "todo" })}
                >
                    <Check />
                </ActionRow>
            </Dialog.Content>
        </Dialog.Positioner>
    </Portal>
</Dialog.Root>

<Sheet
    bind:open={
        () => view.isOpen() && view.meta != null,
        (v) => {
            if (!v) {
                view.back();
            }
        }
    }
    defaultSnapPoint={0.6}
    snapPoints={[0.6, 0.95]}
    background="var(--color-white)"
    grip="var(--color-cream-300)"
>
    {#if view.meta?.entity == "todo"}
        <TodoForm />
    {:else if view.meta?.entity == "event"}
        <EventForm />
    {/if}
</Sheet>
