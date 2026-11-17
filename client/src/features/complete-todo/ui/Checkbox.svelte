<script lang="ts">
    import { Check } from "@lucide/svelte";
    import { TodoService } from "#entities/todo/mod.ts";
    import { Haptics } from "#shared/capabilities/haptics/mod.ts";
    import { tw } from "#shared/lib/styles/tw.ts";
    import { boolAttr } from "runed";

    import type { CalendarDate, ZonedDateTime } from "@internationalized/date";
    import type { ClassName } from "#shared/lib/styles/tw.ts";

    type Props = {
        class?: ClassName;
        id: string;
        completed_at: ZonedDateTime | null;
        occurs_at: CalendarDate | null;
    };

    const { id, completed_at, occurs_at, ...props }: Props = $props();
</script>

<button
    type="button"
    class={tw(
        "group flex size-5.5 shrink-0 items-center justify-center rounded-full border border-cream-950 text-ms data-completed:bg-cream-950 data-completed:text-cream-50",
        props.class
    )}
    data-completed={boolAttr(completed_at)}
    onclick={() => {
        void Haptics.impact("medium");
        void TodoService.complete(id, occurs_at);
    }}
>
    <Check class="group-not-data-completed:hidden" />
</button>
