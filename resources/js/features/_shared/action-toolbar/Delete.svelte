<script lang="ts">
    import { router } from "@inertiajs/svelte";
    import { Trash } from "@lucide/svelte";
    import { destroy } from "$/generated/actions/App/Http/Controllers/TodoController";
    import { m } from "$/paraglide/messages";
    import Confirmable from "$/shared/ui/Confirmable.svelte";

    import Action from "./Action.svelte";

    import type { UrlMethodPair, VisitOptions } from "@inertiajs/core";

    type Props = VisitOptions & {
        href: UrlMethodPair;
        title: string;
        tooltip: string;
    };

    const { tooltip, title, href, ...options }: Props = $props();
</script>

<Confirmable
    {title}
    onconfirm={() => {
        router.visit(href, {
            ...options,
            showProgress: false
        });
        return true;
    }}
>
    {#snippet children(props)}
        <Action {...props()} {tooltip}>
            <Trash />
        </Action>
    {/snippet}
</Confirmable>
