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
        onSelect?: (date: CalendarDate[]) => void;
    };

    const { mode, selected, min, children, onSelect }: Props = $props();

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
        "data-[state=open]:animate-in data-[state=open]:duration-300 data-[state=open]:ease-out data-[state=open]:zoom-in-90 data-[state=open]:fade-in",
        "data-[state=closed]:animate-out data-[state=closed]:duration-200 data-[state=closed]:ease-in-out data-[state=closed]:fade-out"
    ]}
    weekStart={session.current.user.preferences.weekStartOn}
    onSelect={async (date) => {
        open = false;
        onSelect?.(date);
    }}
/>
