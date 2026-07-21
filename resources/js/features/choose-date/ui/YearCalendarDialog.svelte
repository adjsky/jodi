<script lang="ts">
    import { page } from "@inertiajs/svelte";
    import { HistoryView } from "$/shared/inertia/history-view.svelte";
    import { useDeferUntilNextFrame } from "$/shared/lib/hooks.svelte";
    import SheetDialog from "$/shared/ui/SheetDialog.svelte";

    import YearCalendarView from "./YearCalendarView.svelte";

    import type { CalendarDate } from "@internationalized/date";
    import type { Snippet } from "svelte";
    import type { HTMLAttributes } from "svelte/elements";

    type Props = {
        id?: string;
        selected: CalendarDate;
        min?: CalendarDate | null;
        deferHistoryViewFrames?: number;
        children?: Snippet<[() => HTMLAttributes<HTMLElement>]>;
        onSelect?: (date: CalendarDate) => void;
    };

    let {
        id = "general",
        selected,
        min,
        deferHistoryViewFrames = 0,
        children,
        onSelect
    }: Props = $props();

    const view = new HistoryView<{
        __yearcalendardialog: { isOpen: string };
    }>();
    const deferredView = useDeferUntilNextFrame(() => deferHistoryViewFrames);
</script>

<SheetDialog
    bind:open={
        () =>
            deferredView.ready && view.meta?.__yearcalendardialog?.isOpen == id,
        (v) => {
            if (v) {
                void view.push(view.name, {
                    meta: {
                        ...view.meta,
                        __yearcalendardialog: { isOpen: id }
                    }
                });
            } else {
                void view.back();
            }
        }
    }
    height={90}
    portal
    lazyMount
>
    {#snippet trigger(props)}
        {#if children}
            {@render children(props)}
        {/if}
    {/snippet}

    <YearCalendarView
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
