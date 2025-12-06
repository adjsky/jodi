<script lang="ts">
    import { Trash } from "@lucide/svelte";
    import { destroy } from "$/generated/actions/App/Http/Controllers/TodoController";
    import { m } from "$/paraglide/messages";
    import { link } from "$/shared/inertia/link";
    import { optimistic } from "$/shared/inertia/visit/optimistic";

    import { visitOptions } from "../cfg/inertia";
    import Action from "./Action.svelte";

    type Props = {
        todo: App.Data.TodoDto;
    };

    const { todo }: Props = $props();
</script>

<Action
    {@attach link(() => ({
        ...visitOptions,
        ...optimistic(
            (prev) => ({
                todos: prev.todos.filter(
                    (t: App.Data.TodoDto) => t.id != todo.id
                )
            }),
            { error: m["todos.errors.delete"]() }
        ),
        href: destroy(todo.id),
        showProgress: false
    }))}
    tooltip={m["todos.tooltips.delete"]()}
>
    <Trash />
</Action>
