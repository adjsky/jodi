<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import { Bell, Ellipsis, RotateCw, Trash } from "@lucide/svelte";
    import { Todo } from "$/entities/todo";
    import { TodoTime } from "$/features/schedule-todo-time";
    import { Category } from "$/features/select-category";
    import { Color } from "$/features/select-color";
    import { create } from "$/generated/actions/App/Http/Controllers/TodoController";
    import { m } from "$/paraglide/messages";
    import { cleanFormPayload } from "$/shared/lib/form";
    import SaveOrClose from "$/shared/ui/SaveOrClose.svelte";
    import ToolbarAction from "$/shared/ui/ToolbarAction.svelte";

    import type { ZonedDateTime } from "@internationalized/date";
    import type { Snippet } from "svelte";
    import type { HTMLButtonAttributes } from "svelte/elements";

    type Props = {
        day: ZonedDateTime;
        calendar: Snippet<[Snippet<[HTMLButtonAttributes]>]>;
        onClose: VoidFunction;
    };

    let {
        day: scheduledAt = $bindable(),
        calendar: _calendar,
        onClose
    }: Props = $props();
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
        ...cleanFormPayload(data),
        scheduledAt: scheduledAt.toAbsoluteString()
    })}
    onSuccess={() => onClose()}
    class="flex grow flex-col pb-18"
    let:processing
>
    <Todo.Fields {scheduledAt}>
        {#snippet calendar(trigger)}{@render _calendar(trigger)}{/snippet}
        {#snippet close()}
            <SaveOrClose variant="save" disabled={processing} />
        {/snippet}
        {#snippet category()}
            <Category name="category" current={null} />
        {/snippet}
        {#snippet time()}
            <TodoTime bind:scheduledAt hasTime={false} />
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
            <ToolbarAction disabled tooltip={m["todos.tooltips.repeat"]()}>
                <RotateCw />
            </ToolbarAction>
        {/snippet}
        {#snippet color()}
            <Color
                name="color"
                tooltip={m["todos.tooltips.color"]()}
                current={null}
            />
        {/snippet}
        {#snippet notify()}
            <ToolbarAction
                disabled
                tooltip={m["todos.tooltips.notification"]()}
            >
                <Bell />
            </ToolbarAction>
        {/snippet}
        {#snippet more()}
            <ToolbarAction disabled tooltip={m["todos.tooltips.more"]()}>
                <Ellipsis />
            </ToolbarAction>
        {/snippet}
    </Todo.Fields>
</Form>
