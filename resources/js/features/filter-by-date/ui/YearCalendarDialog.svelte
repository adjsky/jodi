<script lang="ts">
    import { page } from "@inertiajs/svelte";
    import { HistoryView } from "$/shared/inertia/history-view.svelte";
    import { DISABLE_SHEET_DRAGGING } from "$/shared/ui/Sheet.svelte";
    import SheetDialog from "$/shared/ui/SheetDialog.svelte";

    import YearCalendar from "./YearCalendar.svelte";

    import type { CalendarDate } from "@internationalized/date";
    import type { Snippet } from "svelte";
    import type { HTMLAttributes } from "svelte/elements";

    type Props = {
        id?: string;
        selected: CalendarDate;
        min?: CalendarDate | null;
        children?: Snippet<[() => HTMLAttributes<HTMLElement>]>;
        onSelect?: (date: CalendarDate) => void;
    };

    let { id = "general", selected, min, children, onSelect }: Props = $props();

    const view = new HistoryView<{
        [DISABLE_SHEET_DRAGGING]: boolean;
        __yearcalendardialog: { isOpen: string };
    }>();
</script>

<SheetDialog
    bind:open={
        () => view.meta?.__yearcalendardialog?.isOpen == id,
        (v) => {
            if (v) {
                void view.push(view.name, {
                    meta: {
                        ...view.meta,
                        [DISABLE_SHEET_DRAGGING]: true,
                        __yearcalendardialog: { isOpen: id }
                    }
                });
            } else {
                void view.back();
            }
        }
    }
    height={90}
    lazyMount
>
    {#snippet trigger(props)}
        {#if children}
            {@render children(props)}
        {/if}
    {/snippet}

    <YearCalendar
        {selected}
        {min}
        portal={false}
        class="absolute h-full rounded-t-2xl bg-white pt-3"
        start={$page.props.auth.user.preferences.weekStartOn}
        onSelect={async (date) => {
            await view.back();
            onSelect?.(date);
        }}
        onClose={() => view.back()}
    />
</SheetDialog>
