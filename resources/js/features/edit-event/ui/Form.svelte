<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import {
        ArrowRight,
        Bell,
        CalendarClock,
        Ellipsis,
        RotateCw
    } from "@lucide/svelte";
    import { Event } from "$/entities/event";
    import Action from "$/features/_shared/action-toolbar/Action.svelte";
    import Color from "$/features/_shared/action-toolbar/Color.svelte";
    import Delete from "$/features/_shared/action-toolbar/Delete.svelte";
    import {
        destroy,
        update
    } from "$/generated/actions/App/Http/Controllers/EventController";
    import { m } from "$/paraglide/messages";
    import { getLocale } from "$/paraglide/runtime";
    import SaveOrClose from "$/shared/ui/SaveOrClose.svelte";
    import Switch from "$/shared/ui/Switch.svelte";
    import TimeInput from "$/shared/ui/TimeInput.svelte";
    import dayjs from "dayjs";

    import { optimistic, visitOptions } from "../cfg/inertia";

    type Props = {
        event: App.Data.EventDto;
        onclose?: VoidFunction;
    };

    const { event, onclose }: Props = $props();

    let isAllDay = $derived(event.isAllDay);
</script>

<Form
    {...optimistic.edit(event.id)}
    action={update(event.id)}
    options={visitOptions}
    showProgress={false}
    let:isDirty
>
    <div class="flex items-center justify-between">
        <h4 class="flex items-center gap-1.5 text-lg font-bold text-cream-800">
            <CalendarClock />
            {new Intl.DateTimeFormat(getLocale(), {
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
                    onclose?.();
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
        <Switch
            bind:checked={isAllDay}
            label={m["events.all-day"]()}
            name="isAllDay"
        />
        <div class="flex items-center gap-2">
            <TimeInput
                name="startsAt"
                defaultValue={dayjs(event.startsAt).format("HH:mm")}
                required
            />
            <ArrowRight class="text-2xl" />
            {#if !isAllDay}
                <TimeInput
                    name="endsAt"
                    defaultValue={event.endsAt
                        ? dayjs(event.endsAt).format("HH:mm")
                        : null}
                    required
                />
            {:else}
                <TimeInput disabled value="00:00" />
            {/if}
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
    <Delete
        title={m["events.delete-ahtung"]()}
        tooltip={m["events.tooltips.delete"]()}
        href={destroy(event.id)}
        {...visitOptions}
        {...optimistic.delete(event.id)}
    />
    <Action disabled tooltip={m["events.tooltips.repeat"]()}>
        <RotateCw />
    </Action>
    <Color
        {...visitOptions}
        {...optimistic.edit(event.id)}
        href={update(event.id)}
        tooltip={m["events.tooltips.color"]()}
        current={event.color}
        preserveUrl
    />
    <Action disabled tooltip={m["events.tooltips.notification"]()}>
        <Bell />
    </Action>
    <Action tooltip={m["events.tooltips.more"]()}><Ellipsis /></Action>
</div>
