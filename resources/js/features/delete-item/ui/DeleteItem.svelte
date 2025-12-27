<script lang="ts">
    import { router } from "@inertiajs/svelte";
    import { Trash } from "@lucide/svelte";
    import Confirmable from "$/shared/ui/Confirmable.svelte";
    import ToolbarAction from "$/shared/ui/ToolbarAction.svelte";

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
        <ToolbarAction {...props()} {tooltip}>
            <Trash />
        </ToolbarAction>
    {/snippet}
</Confirmable>
