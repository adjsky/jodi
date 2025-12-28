<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import { Bell, Ellipsis, RotateCw, Trash } from "@lucide/svelte";
    import { Todo } from "$/entities/todo";
    import { Category } from "$/features/select-category";
    import { Color } from "$/features/select-color";
    import { create } from "$/generated/actions/App/Http/Controllers/TodoController";
    import { m } from "$/paraglide/messages";
    import SaveOrClose from "$/shared/ui/SaveOrClose.svelte";
    import ToolbarAction from "$/shared/ui/ToolbarAction.svelte";

    import { view } from "../model/view";

    import type { CalendarDate } from "@internationalized/date";

    type Props = {
        day: CalendarDate;
    };

    const { day }: Props = $props();
</script>

<Form
    action={create()}
    options={{
        only: ["todos"],
        preserveState: true,
        preserveScroll: true,
        replace: true
    }}
    let:processing
>
    <Todo.Fields
        date={day}
        onCalendarOpen={() => {
            view.updateMeta(
                { entity: "todo", overlay: "calendar" },
                { push: true, viewTransition: true }
            );
        }}
    >
        {#snippet close()}
            <SaveOrClose variant="save" disabled={processing} />
        {/snippet}
        {#snippet category()}
            <Category name="category" />
        {/snippet}
        {#snippet destroy()}
            <ToolbarAction
                tooltip={m["todos.tooltips.delete"]()}
                onclick={() => view.back()}
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
