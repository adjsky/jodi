<script lang="ts">
    import { SessionCache } from "#entities/user/mod.ts";

    import Calendar from "./Calendar.svelte";

    import type { CalendarMode } from "../model/types";
    import type { CalendarDate } from "@internationalized/date";
    import type { Snippet } from "svelte";
    import type { HTMLAttributes } from "svelte/elements";

    type Props = {
        mode: CalendarMode;
        selected: CalendarDate[];
        min?: CalendarDate | null;
        children: Snippet<[HTMLAttributes<HTMLElement>]>;
        on_select?: (date: CalendarDate[]) => void;
    };

    const { mode, selected, min, children, on_select }: Props = $props();

    const session = SessionCache.retain();

    let open = $state(false);
</script>

{@render children({
    onclick() {
        open = true;
    }
})}

<Calendar
    bind:open
    {mode}
    {selected}
    {min}
    class={[
        "origin-center",
        "data-[state=open]:animate-in data-[state=open]:ease-out data-[state=open]:animation-duration-300 data-[state=open]:zoom-in-90 data-[state=open]:fade-in",
        "data-[state=closed]:animate-out data-[state=closed]:ease-in-out data-[state=closed]:animation-duration-200 data-[state=closed]:fade-out"
    ]}
    week_start={session.current.user.preferences.weekStartOn}
    confirm="manual"
    on_select={async (date) => {
        open = false;
        on_select?.(date);
    }}
/>
