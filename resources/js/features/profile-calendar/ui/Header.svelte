<script lang="ts">
    import { CalendarIcon } from "@lucide/svelte";
    import Profile from "$/shared/ui/Profile.svelte";
    import { capitalize } from "remeda";

    import type { Calendar } from "../helpers/calendar.svelte";

    type Props = {
        calendar: Calendar;
    };

    const { calendar }: Props = $props();
</script>

<header class="flex h-12.5 items-center justify-between px-5">
    <button><CalendarIcon class="text-2xl" /></button>
    <div class="absolute left-1/2 -translate-x-1/2">
        <h1 class="text-center text-lg font-bold">
            {capitalize(calendar.pointer.format("dddd"))}
        </h1>
        <h2 class="text-center text-xs text-cream-600">
            {new Intl.DateTimeFormat(calendar.pointer.locale(), {
                year: "numeric",
                month: "long"
            }).format(calendar.pointer.toDate())}
        </h2>
    </div>
    <Profile />
</header>
