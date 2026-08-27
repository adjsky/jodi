<script lang="ts">
    import { page } from "@inertiajs/svelte";
    import { HistoryView } from "$/shared/integrations/inertia";
    import { DeferUntilNextFrame } from "$/shared/lib/svelte/defer-until-next-frame.svelte";

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
        children: Snippet<[HTMLAttributes<HTMLElement>]>;
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
        __calendarpicker: { isOpen: string };
    }>();
    const deferredView = new DeferUntilNextFrame(() => deferHistoryViewFrames);
</script>

{@render children({
    onclick() {
        void view.push(view.name, {
            meta: {
                ...view.meta,
                __calendarpicker: { isOpen: id }
            }
        });
    }
})}

<Calendar
    bind:open={
        () => deferredView.ready && view.meta?.__calendarpicker?.isOpen == id,
        () => view.back()
    }
    {mode}
    {selected}
    {min}
    class={[
        "origin-center",
        "data-[state=open]:animate-in data-[state=open]:duration-300 data-[state=open]:ease-[cubic-bezier(0.32,0.72,0,1)] data-[state=open]:zoom-in-95 data-[state=open]:fade-in",
        "data-[state=closed]:animate-out data-[state=closed]:duration-200 data-[state=closed]:ease-in-out data-[state=closed]:fade-out"
    ]}
    weekStart={$page.props.auth.user.preferences.weekStartOn}
    onSelect={async (date) => {
        await view.back();
        onSelect?.(date);
    }}
/>
