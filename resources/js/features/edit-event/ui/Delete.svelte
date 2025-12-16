<script lang="ts">
    import { router } from "@inertiajs/svelte";
    import { Trash } from "@lucide/svelte";
    import { destroy } from "$/generated/actions/App/Http/Controllers/EventController";
    import { m } from "$/paraglide/messages";
    import Confirmable from "$/shared/ui/Confirmable.svelte";

    import { optimistic, visitOptions } from "../cfg/inertia";
    import Action from "./Action.svelte";

    type Props = {
        event: App.Data.EventDto;
    };

    const { event }: Props = $props();
</script>

<Confirmable
    title={m["events.delete-ahtung"]()}
    onconfirm={() => {
        router.visit(destroy(event.id), {
            ...visitOptions,
            ...optimistic.delete(event.id),
            showProgress: false
        });
        return true;
    }}
>
    {#snippet children(props)}
        <Action {...props()} tooltip={m["events.tooltips.delete"]()}>
            <Trash />
        </Action>
    {/snippet}
</Confirmable>
