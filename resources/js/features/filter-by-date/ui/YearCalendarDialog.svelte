<script lang="ts">
    import { Dialog } from "@ark-ui/svelte";
    import { page } from "@inertiajs/svelte";

    import YearCalendar from "./YearCalendar.svelte";

    import type { CalendarDate, DateValue } from "@internationalized/date";

    type Props = {
        open: boolean;
        selected: CalendarDate;
        onSelect?: (date: DateValue) => void;
    };

    let { open = $bindable(), selected, onSelect }: Props = $props();
</script>

<Dialog.Root lazyMount bind:open>
    <Dialog.Backdrop
        class={[
            "fixed inset-0 z-100 bg-cream-950/60 duration-300",
            "data-[state=closed]:animate-out data-[state=closed]:fade-out",
            "data-[state=open]:animate-in data-[state=open]:fade-in"
        ]}
    />
    <Dialog.Positioner>
        <Dialog.Content
            class={[
                "fixed inset-x-0 bottom-0 z-100 h-[95%] duration-300",
                "data-[state=closed]:animate-out data-[state=closed]:slide-out-to-bottom",
                "data-[state=open]:animate-in data-[state=open]:slide-in-from-bottom"
            ]}
        >
            <YearCalendar
                {selected}
                {onSelect}
                portal={false}
                class="absolute inset-0 rounded-t-2xl bg-white"
                start={$page.props.auth.user.preferences.weekStartOn}
                onClose={() => (open = false)}
            />
        </Dialog.Content>
    </Dialog.Positioner>
</Dialog.Root>
