<script lang="ts">
    import { Form, router } from "@inertiajs/svelte";
    import { toCalendarDate } from "@internationalized/date";
    import { Trash } from "@lucide/svelte";
    import { Todo } from "$/entities/todo";
    import { YearCalendarDialog } from "$/features/filter-by-date";
    import { RescheduleItem } from "$/features/reschedule-item";
    import { TodoTime } from "$/features/schedule-todo-time";
    import { Category } from "$/features/select-category";
    import { Color } from "$/features/select-color";
    import { Recurrence } from "$/features/select-recurrence";
    import Reminder from "$/features/select-reminder/ui/Reminder.svelte";
    import { create } from "$/generated/actions/App/Http/Controllers/TodoController";
    import { m } from "$/paraglide/messages";
    import {
        NOTIFICATION_DEFAULT_SUBHOURS,
        WEEK_CAROUSEL_CACHE_TAG
    } from "$/shared/cfg/constants";
    import { normalizeIsoString, timediff } from "$/shared/lib/date";
    import * as PushSubscription from "$/shared/lib/push-subscription.svelte";
    import { toaster } from "$/shared/lib/toaster";
    import SaveOrClose from "$/shared/ui/SaveOrClose.svelte";
    import ToolbarAction from "$/shared/ui/ToolbarAction.svelte";

    import type { ZonedDateTime } from "@internationalized/date";

    type Props = {
        day: ZonedDateTime;
        onClose: VoidFunction;
    };

    let { day: scheduledAt = $bindable(), onClose }: Props = $props();

    let hasTime = $state(false);
    let notifyAt = $state<ZonedDateTime | null>(null);
</script>

<Form
    action={create()}
    options={{
        only: ["todos", "categories"],
        preserveState: true,
        preserveScroll: true,
        preserveUrl: true,
        replace: true
    }}
    transform={(data) => ({
        ...data,
        hasTime,
        scheduledAt: hasTime
            ? normalizeIsoString(scheduledAt.toAbsoluteString())
            : toCalendarDate(scheduledAt).toString()
    })}
    onSuccess={() => {
        if (notifyAt) {
            PushSubscription.ahtung(m["todos.reminder-ahtung"]());
        }

        router.flushByCacheTags(WEEK_CAROUSEL_CACHE_TAG);

        onClose();
    }}
    class="flex grow flex-col pb-18"
    let:processing
>
    <Todo.Fields {scheduledAt}>
        {#snippet calendar(trigger)}
            <YearCalendarDialog
                selected={toCalendarDate(scheduledAt)}
                onSelect={(d) => {
                    if (notifyAt) {
                        notifyAt = notifyAt.set(d);
                    }
                    scheduledAt = scheduledAt.set(d);
                }}
            >
                {#snippet children(props)}
                    {@render trigger(props())}
                {/snippet}
            </YearCalendarDialog>
        {/snippet}
        {#snippet close()}
            <SaveOrClose variant="save" disabled={processing} />
        {/snippet}
        {#snippet category()}
            <Category name="category" current={null} />
        {/snippet}
        {#snippet time()}
            <TodoTime
                {scheduledAt}
                bind:hasTime
                onChange={(time, hasTime) => {
                    if (!hasTime) {
                        notifyAt = null;
                    } else {
                        if (notifyAt) {
                            notifyAt = notifyAt.add(
                                timediff(scheduledAt, time)
                            );
                        } else {
                            notifyAt = scheduledAt.set(time).subtract({
                                hours: NOTIFICATION_DEFAULT_SUBHOURS
                            });
                        }
                    }

                    scheduledAt = scheduledAt.set(time);
                }}
            />
        {/snippet}
        {#snippet destroy()}
            <ToolbarAction
                tooltip={m["todos.tooltips.delete"]()}
                onclick={onClose}
            >
                <Trash />
            </ToolbarAction>
        {/snippet}
        {#snippet repeat()}
            <Recurrence
                day={scheduledAt}
                name="rrule"
                tooltip={m["todos.tooltips.repeat"]()}
            />
        {/snippet}
        {#snippet color()}
            <Color name="color" tooltip={m["todos.tooltips.color"]()} />
        {/snippet}
        {#snippet notify()}
            <Reminder
                bind:notifyAt
                startsAt={scheduledAt}
                name="notifyAt"
                tooltip={m["todos.tooltips.notification"]()}
                beforeOpen={() => {
                    if (!hasTime) {
                        toaster.info(m["todos.reminder.select-time"]());
                        return false;
                    }
                }}
            />
        {/snippet}
        {#snippet more()}
            <RescheduleItem
                startsAt={toCalendarDate(scheduledAt)}
                tooltip={m["todos.tooltips.more"]()}
                onReschedule={(d) => {
                    if (notifyAt) {
                        notifyAt = notifyAt.set(d);
                    }
                    scheduledAt = scheduledAt.set(d);
                }}
            />
        {/snippet}
    </Todo.Fields>
</Form>
