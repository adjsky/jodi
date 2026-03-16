<script lang="ts">
    import { router } from "@inertiajs/svelte";
    import { Trash } from "@lucide/svelte";
    import { HistoryView } from "$/shared/inertia/history-view.svelte";
    import RecurrenceActionDialog from "$/shared/ui/RecurrenceActionDialog.svelte";
    import ToolbarAction from "$/shared/ui/ToolbarAction.svelte";

    import type { UrlMethodPair, VisitOptions } from "@inertiajs/core";

    type Props = VisitOptions & {
        href: string | UrlMethodPair;
        title: {
            recurring: string;
            general: string;
        };
        tooltip: string;
        recurring: boolean;
        occursAt: string | null;
        scopeLabels: { this: string; all: string };
    };

    const {
        href,
        title,
        tooltip,
        recurring,
        occursAt,
        scopeLabels,
        ...options
    }: Props = $props();

    const view = new HistoryView<{ __deleteitem: { isOpen: boolean } }>();
</script>

<RecurrenceActionDialog
    {scopeLabels}
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
    title={recurring ? title.recurring : title.general}
    fallback={!recurring}
    onConfirm={(scope) => {
        void router.visit(href, {
            ...options,
            data: { scope, occursAt },
            showProgress: false
        });
        return true;
    }}
>
    {#snippet trigger(props)}
        <ToolbarAction {...props()} {tooltip}>
            <Trash />
        </ToolbarAction>
    {/snippet}
</RecurrenceActionDialog>
