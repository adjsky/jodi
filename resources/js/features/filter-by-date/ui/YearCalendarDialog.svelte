<script lang="ts">
    import { Dialog } from "@ark-ui/svelte";
    import { page } from "@inertiajs/svelte";
    import { HistoryView } from "$/shared/inertia/history-view.svelte";
    import { DISABLE_SHEET_DRAGGING } from "$/shared/ui/Sheet.svelte";

    import YearCalendar from "./YearCalendar.svelte";

    import type { CalendarDate } from "@internationalized/date";
    import type { Snippet } from "svelte";
    import type { HTMLButtonAttributes } from "svelte/elements";

    type Props = {
        selected: CalendarDate;
        children?: Snippet<[() => HTMLButtonAttributes]>;
        onSelect?: (date: CalendarDate) => void;
    };

    let { selected, children, onSelect }: Props = $props();

    const view = new HistoryView<{
        [DISABLE_SHEET_DRAGGING]: boolean;
        __yearcalendardialog: { isOpen: boolean };
    }>();
</script>

<Dialog.Root
    lazyMount
    bind:open={
        () => view.meta?.__yearcalendardialog?.isOpen ?? false,
        (v) => {
            if (v) {
                void view.push(view.name, {
                    ...view.meta,
                    [DISABLE_SHEET_DRAGGING]: true,
                    __yearcalendardialog: { isOpen: true }
                });
            } else {
                void view.back();
            }
        }
    }
>
    {#if children}
        <Dialog.Trigger>
            {#snippet asChild(props)}{@render children(props)}{/snippet}
        </Dialog.Trigger>
    {/if}

    <Dialog.Positioner>
        <Dialog.Backdrop
            class={[
                "fixed inset-0 z-100 bg-cream-950/60 duration-300",
                "data-[state=closed]:animate-out data-[state=closed]:fade-out",
                "data-[state=open]:animate-in data-[state=open]:fade-in"
            ]}
        />
        <Dialog.Content
            class={[
                "fixed inset-x-0 bottom-0 z-100 h-[90%] duration-300",
                "data-[state=closed]:animate-out data-[state=closed]:slide-out-to-bottom",
                "data-[state=open]:animate-in data-[state=open]:slide-in-from-bottom"
            ]}
        >
            <YearCalendar
                {selected}
                portal={false}
                class="absolute h-full rounded-t-2xl bg-white pt-3"
                start={$page.props.auth.user.preferences.weekStartOn}
                onSelect={async (date) => {
                    await view.back();
                    onSelect?.(date);
                }}
                onClose={() => view.back()}
            />
        </Dialog.Content>
    </Dialog.Positioner>
</Dialog.Root>
