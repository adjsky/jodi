<script lang="ts">
    import { inertia } from "@inertiajs/svelte";
    import { Check } from "@lucide/svelte";
    import { complete } from "$/generated/actions/App/Http/Controllers/TodoController";
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
    use:inertia={{
        only: ["todos"],
        preserveState: true,
        preserveScroll: true,
        replace: true,
        href: complete(todo.id),
        __jodi_keepHash: true
    }}
    type="button"
    disabled={loading}
    class={tw(
        "flex size-4.5 shrink-0 items-center justify-center rounded-full border border-cream-950 data-completed:bg-cream-950 data-completed:text-cream-50",
        props.class
    )}
    data-completed={boolAttr(todo.completedAt)}
>
    {#if todo.completedAt}
        <Check class="text-sm" />
    {/if}
</button>
