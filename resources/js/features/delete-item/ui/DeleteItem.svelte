<script lang="ts">
    import { router } from "@inertiajs/svelte";
    import { Trash } from "@lucide/svelte";
    import { HistoryView } from "$/shared/inertia/history-view.svelte";
    import Confirmable from "$/shared/ui/Confirmable.svelte";
    import ToolbarAction from "$/shared/ui/ToolbarAction.svelte";

    import type { UrlMethodPair, VisitOptions } from "@inertiajs/core";

    type Props = VisitOptions & {
        href: string | UrlMethodPair;
        title: string;
        tooltip: string;
    };

    const { tooltip, title, href, ...options }: Props = $props();

    const view = new HistoryView<{ __deleteitem: { isOpen: boolean } }>();
</script>

<Confirmable
    {title}
    bind:open={
        () => view.meta?.__deleteitem?.isOpen ?? false,
        (v) => {
            if (v) {
                void view.push(view.name, {
                    ...view.meta,
                    __deleteitem: { isOpen: true }
                });
            } else {
                void view.back();
            }
        }
    }
    onConfirm={() => {
        void router.visit(href, {
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
