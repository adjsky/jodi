<script lang="ts">
    import { router } from "@inertiajs/svelte";
    import { parseAbsolute, toCalendarDate } from "@internationalized/date";
    import { Trash } from "@lucide/svelte";
    import { TIMEZONE } from "$/shared/cfg/constants";
    import { HistoryView } from "$/shared/inertia/history-view.svelte";
    import { useDeferUntilNextFrame } from "$/shared/lib/hooks.svelte";
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
        date?: string;
        scopeLabels: { this: string; following: string; all: string };
        deferHistoryViewFrames?: number;
    };

    const {
        href,
        title,
        tooltip,
        recurring,
        occursAt,
        date,
        scopeLabels,
        deferHistoryViewFrames = 0,
        ...options
    }: Props = $props();

    const view = new HistoryView<{ __deleteitem: { isOpen: boolean } }>();
    const deferredView = useDeferUntilNextFrame(() => deferHistoryViewFrames);
</script>

<RecurrenceActionDialog
    {scopeLabels}
    bind:open={
        () => deferredView.ready && (view.meta?.__deleteitem?.isOpen ?? false),
        (v) => {
            if (v) {
                void view.push(view.name, {
                    meta: { ...view.meta, __deleteitem: { isOpen: true } }
                });
            } else {
                void view.back();
            }
        }
    }
    title={recurring ? title.recurring : title.general}
    fallback={!recurring}
    onConfirm={async (scope) => {
        await router.visit(href, {
            ...options,
            data: {
                scope,
                occursAt,
                date: date
                    ? toCalendarDate(parseAbsolute(date, TIMEZONE)).toString()
                    : null
            },
            showProgress: true
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
