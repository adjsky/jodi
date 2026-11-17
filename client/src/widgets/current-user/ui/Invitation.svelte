<script lang="ts">
    import { Clipboard } from "@ark-ui/svelte";
    import { CheckIcon, ClipboardCopyIcon } from "@lucide/svelte";
    import { createMutation, createQuery } from "@tanstack/svelte-query";
    import { m } from "#paraglide/messages.js";
    import { Overlay } from "#shared/capabilities/overlay/mod.svelte.ts";
    import { raise } from "#shared/lib/exception/raise.ts";
    import Button from "#shared/ui/Button.svelte";
    import { ScreenView } from "#shared/ui/composites/screen-view/mod.ts";
    import Confirmable from "#shared/ui/Confirmable.svelte";
    import ResourceError from "#shared/ui/ResourceError.svelte";
    import Skeleton from "#shared/ui/Skeleton.svelte";

    import {
        delete_invitation_mutation_options,
        invitations_query_options
    } from "../api/invitations";

    let is_confirming_deletion = $state(false);

    const id = $derived(Overlay.meta?.invitation?.id);

    const invitations = createQuery(() => ({
        ...invitations_query_options,
        enabled: Boolean(id)
    }));

    const delete_invitation_mutation = createMutation(() => delete_invitation_mutation_options);

    const invitation = $derived(invitations.data?.find((invitation) => invitation.id === id));

    const is_error = $derived(!invitations.data && invitations.error);
    const is_loading = $derived(invitations.isLoading);
    const is_not_found = $derived(invitations.data && !invitation);
</script>

<ScreenView.Overlay bind:open={() => Boolean(id), () => Overlay.close()}>
    <ScreenView.Header title={m["current-user.invitations.invitation"]()} />
    <ScreenView.Content
        class={[is_error || is_not_found ? "justify-center" : "mt-5"]}
        aria-live="polite"
        aria-busy={invitations.isFetching}
    >
        {#if is_error}
            <ResourceError
                message={m["current-user.invitations.errors.list"]()}
                on_retry={() => invitations.refetch()}
            />
        {:else if is_not_found}
            <ResourceError message={m["current-user.invitations.errors.not-found"]()} />
        {:else}
            <!-- TODO: maybe use web share API? -->
            <Clipboard.Root value={invitation?.shareUrl ?? ""}>
                <Clipboard.Label class="font-semibold">
                    {m["current-user.invitations.share"]()}:
                </Clipboard.Label>
                <Clipboard.Control
                    class="mt-2 flex items-center gap-1 rounded-lg border border-cream-950 bg-white p-2"
                >
                    {#if is_loading}
                        <Skeleton grow inline={false} />
                    {:else}
                        <Clipboard.ValueText class="truncate font-medium" />
                    {/if}
                    <Clipboard.Trigger class="shrink-0 p-1" disabled={is_loading}>
                        <Clipboard.Indicator>
                            {#snippet copied()}
                                <CheckIcon class="text-xl text-green" />
                            {/snippet}
                            <ClipboardCopyIcon class="text-xl" />
                        </Clipboard.Indicator>
                    </Clipboard.Trigger>
                </Clipboard.Control>
            </Clipboard.Root>
        {/if}
    </ScreenView.Content>

    {#if !is_not_found}
        <ScreenView.Footer class="mt-5">
            <Confirmable
                bind:open={is_confirming_deletion}
                title={m["current-user.invitations.delete-ahtung"]()}
                on_confirm={async () => {
                    if (!invitation) raise("No invitation available.");
                    const id = invitation.id;
                    await Overlay.close();
                    is_confirming_deletion = false;
                    delete_invitation_mutation.mutate(id);
                }}
            >
                {#snippet trigger(props)}
                    <Button {...props()} type="button" class="shrink-0" disabled={!invitation}>
                        {m["current-user.invitations.delete"]()}
                    </Button>
                {/snippet}
            </Confirmable>
        </ScreenView.Footer>
    {/if}
</ScreenView.Overlay>
