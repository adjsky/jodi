<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import { parseAbsoluteToLocal } from "@internationalized/date";
    import { ArrowRight, Bell, Ellipsis, RotateCw } from "@lucide/svelte";
    import { Event } from "$/entities/event";
    import { DeleteItem } from "$/features/delete-item";
    import { Color } from "$/features/select-color";
    import {
        destroy as _destroy,
        update
    } from "$/generated/actions/App/Http/Controllers/EventController";
    import { m } from "$/paraglide/messages";
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
        <Event.Fields
            title={event.title}
            description={event.description}
            startsAt={parseAbsoluteToLocal(event.startsAt)}
            onCalendarOpen={() => {}}
        >
            {#snippet close()}
                <SaveOrClose
                    variant={isDirty ? "save" : "close"}
                    onclick={() => {
                        if (!isDirty) {
                            open = false;
                        }
                    }}
                />
            {/snippet}
            {#snippet timePicker()}
                <div class="flex items-center gap-1.5">
                    <TimeInput
                        name="startsAt"
                        defaultValue={formatToHHMM(
                            parseAbsoluteToLocal(event.startsAt).toDate()
                        )}
                        required
                    />
                    <ArrowRight class="text-2xl" />
                    <TimeInput
                        name="endsAt"
                        defaultValue={event.endsAt
                            ? formatToHHMM(
                                  parseAbsoluteToLocal(event.endsAt).toDate()
                              )
                            : null}
                        required
                    />
                </div>
            {/snippet}
            {#snippet destroy()}
                <DeleteItem
                    title={m["events.delete-ahtung"]()}
                    tooltip={m["events.tooltips.delete"]()}
                    href={_destroy(event.id)}
                    {...visitOptions}
                    {...optimistic.delete(event.id)}
                />
            {/snippet}
            {#snippet repeat()}
                <ToolbarAction disabled tooltip={m["events.tooltips.repeat"]()}>
                    <RotateCw />
                </ToolbarAction>
            {/snippet}
            {#snippet color()}
                <Color
                    {...visitOptions}
                    {...optimistic.edit(event.id, true)}
                    href={update(event.id)}
                    tooltip={m["events.tooltips.color"]()}
                    current={event.color}
                    preserveUrl
                />
            {/snippet}
            {#snippet notify()}
                <ToolbarAction
                    disabled
                    tooltip={m["events.tooltips.notification"]()}
                >
                    <Bell />
                </ToolbarAction>
            {/snippet}
            {#snippet more()}
                <ToolbarAction disabled tooltip={m["events.tooltips.more"]()}>
                    <Ellipsis />
                </ToolbarAction>
            {/snippet}
        </Event.Fields>
    </Form>
</Sheet>
