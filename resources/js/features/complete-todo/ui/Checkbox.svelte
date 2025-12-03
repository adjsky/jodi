<script lang="ts">
    import { Check } from "@lucide/svelte";
    import { complete } from "$/generated/actions/App/Http/Controllers/TodoController";
    import { m } from "$/paraglide/messages";
    import { link } from "$/shared/inertia/link";
    import { optimistic } from "$/shared/inertia/visit/optimistic";
    import { tw } from "$/shared/lib/styles";
    import { boolAttr } from "runed";

    import type { ClassName } from "$/shared/lib/styles";

    type Props = {
        loading?: boolean;
        todo: App.Data.TodoDto;
        class?: ClassName;
    };

    const { loading, todo, ...props }: Props = $props();
</script>

<button
    {@attach link(() => ({
        ...optimistic(
            (prev) => ({
                todos: prev.todos.map((t: App.Data.TodoDto) =>
                    t.id == todo.id
                        ? {
                              ...t,
                              completedAt: t.completedAt
                                  ? null
                                  : new Date().toISOString()
                          }
                        : t
                )
            }),
            { error: m["todos.errors.complete"]() }
        ),
        href: complete(todo.id),
        only: ["todos"],
        preserveUrl: true,
        preserveState: true,
        preserveScroll: true,
        replace: true,
        showProgress: false
    }))}
    type="button"
    disabled={loading}
    class={tw(
        "group flex size-5.5 shrink-0 items-center justify-center rounded-full border border-cream-950 text-ms data-completed:bg-cream-950 data-completed:text-cream-50",
        props.class
    )}
    data-completed={boolAttr(todo.completedAt)}
>
    <Check class="group-not-data-completed:hidden" />
</button>
