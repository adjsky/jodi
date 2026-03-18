<script lang="ts">
    import { router } from "@inertiajs/svelte";
    import { parseAbsolute, toCalendarDate } from "@internationalized/date";
    import { Trash } from "@lucide/svelte";
    import { TIMEZONE } from "$/shared/cfg/constants";
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
        date: string;
        scopeLabels: { this: string; all: string };
    };

    const {
        href,
        title,
        tooltip,
        recurring,
        date,
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
        const utcOccursAt = toCalendarDate(parseAbsolute(date, "UTC"));
        const localOccursAt = toCalendarDate(parseAbsolute(date, TIMEZONE));

        void router.visit(href, {
            ...options,
            data: {
                scope,
                occursAt: {
                    utc: utcOccursAt.toString(),
                    local: localOccursAt.toString()
                }
            },
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
