<script lang="ts">
    import { page } from "@inertiajs/svelte";
    import { HistoryView } from "$/shared/integrations/inertia";
    import { DeferUntilNextFrame } from "$/shared/lib/svelte/defer-until-next-frame.svelte";
    import SheetDialog from "$/shared/ui/SheetDialog.svelte";

    import Calendar from "./Calendar.svelte";

    import type { CalendarMode } from "../model/types";
    import type { CalendarDate } from "@internationalized/date";
    import type { Snippet } from "svelte";
    import type { HTMLAttributes } from "svelte/elements";

    type Props = {
        id?: string;
        mode: CalendarMode;
        selected: CalendarDate[];
        min?: CalendarDate | null;
        deferHistoryViewFrames?: number;
        children?: Snippet<[() => HTMLAttributes<HTMLElement>]>;
        onSelect?: (date: CalendarDate[]) => void;
    };

    let {
        id = "general",
        mode,
        selected,
        min,
        deferHistoryViewFrames = 0,
        children,
        onSelect
    }: Props = $props();

    const view = new HistoryView<{
        __yearcalendardialog: { isOpen: string };
    }>();
    const deferredView = new DeferUntilNextFrame(() => deferHistoryViewFrames);
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
    unmountOnExit
>
    {#snippet trigger(props)}
        {#if children}
            {@render children(props)}
        {/if}
    {/snippet}

    <Calendar
        {mode}
        {selected}
        {min}
        portal={false}
        class="absolute h-full rounded-t-2xl bg-white pt-3"
        weekStart={$page.props.auth.user.preferences.weekStartOn}
        onSelect={async (date) => {
            await view.back();
            onSelect?.(date);
        }}
        onClose={() => view.back()}
    />
</SheetDialog>
