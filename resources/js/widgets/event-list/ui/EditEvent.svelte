<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import { DateFormatter } from "@internationalized/date";
    import {
        ArrowRight,
        CalendarClock,
        Ellipsis,
        RotateCw,
        UserPlus
    } from "@lucide/svelte";
    import { Event } from "$/entities/event";
    import { DeleteItem } from "$/features/delete-item";
    import { Color } from "$/features/select-color";
    import {
        destroy,
        update
    } from "$/generated/actions/App/Http/Controllers/EventController";
    import { m } from "$/paraglide/messages";
    import { getLocale } from "$/paraglide/runtime";
    import { formatToHHMM } from "$/shared/lib/date";
    import SaveOrClose from "$/shared/ui/SaveOrClose.svelte";
    import Sheet from "$/shared/ui/Sheet.svelte";
    import TimeInput from "$/shared/ui/TimeInput.svelte";
    import ToolbarAction from "$/shared/ui/ToolbarAction.svelte";

    import { optimistic, visitOptions } from "../cfg/inertia";

    type Props = {
        open: boolean;
        event: App.Data.EventDto;
    };

    let { open = $bindable(), event }: Props = $props();
</script>

<Sheet
    bind:open
    defaultSnapPoint={0.6}
    snapPoints={[0.6, 0.95]}
    background="var(--color-white)"
    grip="var(--color-cream-300)"
>
    <Form
        {...optimistic.edit(event.id)}
        action={update(event.id)}
        options={visitOptions}
        showProgress={false}
        transform={(data) => ({
            ...data,
            startsAt: data.startsAt || undefined,
            endsAt: data.endsAt || undefined
        })}
        let:isDirty
    >
        <div class="flex items-center justify-between">
            <h4 class="flex items-center gap-1.5 text-lg font-bold">
                <CalendarClock />
                {new DateFormatter(getLocale(), {
                    day: "2-digit",
                    year: "numeric",
                    month: "short",
                    weekday: "short"
                }).format(new Date(event.startsAt))}
            </h4>
            <SaveOrClose
                variant={isDirty ? "save" : "close"}
                onclick={() => {
                    if (!isDirty) {
                        open = false;
                    }
                }}
            />
        </div>
        <Event.Title
            class="mt-5"
            name="title"
            defaultValue={event.title}
            required
        />
        <div class="mt-3 flex items-center gap-4 text-lg">
            <div class="flex items-center gap-2">
                <TimeInput
                    name="startsAt"
                    defaultValue={formatToHHMM(new Date(event.startsAt))}
                    required
                />
                <ArrowRight class="text-2xl" />
                <TimeInput
                    name="endsAt"
                    defaultValue={event.endsAt
                        ? formatToHHMM(new Date(event.endsAt))
                        : null}
                    required
                />
            </div>
        </div>
        <Event.Description
            name="description"
            class="mt-6"
            defaultValue={event.description ?? ""}
        />
    </Form>

    <div
        class="absolute inset-x-0 bottom-0 z-10 flex items-end justify-between bg-white px-4 pb-6"
    >
        <DeleteItem
            title={m["events.delete-ahtung"]()}
            tooltip={m["events.tooltips.delete"]()}
            href={destroy(event.id)}
            {...visitOptions}
            {...optimistic.delete(event.id)}
        />
        <ToolbarAction disabled tooltip={m["events.tooltips.repeat"]()}>
            <RotateCw />
        </ToolbarAction>
        <Color
            {...visitOptions}
            {...optimistic.edit(event.id, true)}
            href={update(event.id)}
            tooltip={m["events.tooltips.color"]()}
            current={event.color}
            preserveUrl
        />
        <ToolbarAction disabled tooltip={m["events.tooltips.notification"]()}>
            <UserPlus />
        </ToolbarAction>
        <ToolbarAction disabled tooltip={m["events.tooltips.more"]()}>
            <Ellipsis />
        </ToolbarAction>
    </div>
</Sheet>
