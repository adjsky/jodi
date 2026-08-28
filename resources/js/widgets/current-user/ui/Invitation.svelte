<script lang="ts">
    import { Clipboard } from "@ark-ui/svelte";
    import { CheckIcon, ClipboardCopyIcon } from "@lucide/svelte";
    import { createMutation, createQuery } from "@tanstack/svelte-query";
    import { m } from "$/paraglide/messages";
    import { ScreenView } from "$/shared/composites/screen-view";
    import { HistoryView } from "$/shared/integrations/inertia";
    import Button from "$/shared/ui/Button.svelte";
    import Confirmable from "$/shared/ui/Confirmable.svelte";
    import ResourceError from "$/shared/ui/ResourceError.svelte";
    import Skeleton from "$/shared/ui/Skeleton.svelte";

    import {
        deleteInvitationMutationOptions,
        invitationsQueryOptions
    } from "../api/invitations";
    import { view } from "../model/view";

    import type { ViewProps } from "../model/view";

    type Props = ViewProps;

    let { open = $bindable() }: Props = $props();

    const invitations = createQuery(() => ({
        ...invitationsQueryOptions,
        enabled: open
    }));

    const deleteInvitationMutation = createMutation(
        () => deleteInvitationMutationOptions
    );

    const deleteView = new HistoryView<{
        __deleteinvitation: { isOpen: boolean };
    }>();

    let confirmedIdToDelete: string | null = $state(null);

    const id = $derived.by(() => {
        const [_, __, id] = view.name.split("/");
        return id;
    });

    const invitation = $derived(
        invitations.data?.find((invitation) => invitation.id == id)
    );

    const isError = $derived(!invitations.data && invitations.error);
    const isLoading = $derived(invitations.isLoading);
    const isNotFound = $derived(invitations.data && !invitation);
</script>

<ScreenView.Overlay bind:open>
    <ScreenView.Header title={m["current-user.invitations.invitation"]()} />
    <ScreenView.Content
        class={[isError || isNotFound ? "justify-center" : "mt-5"]}
        aria-live="polite"
        aria-busy={invitations.isFetching}
    >
        {#if isError}
            <ResourceError
                message={m["current-user.invitations.list-error"]()}
                onRetry={() => invitations.refetch()}
            />
        {:else if isNotFound}
            <ResourceError
                message={m["current-user.invitations.not-found"]()}
            />
        {:else}
            <!-- TODO: maybe use web share API? -->
            <Clipboard.Root value={invitation?.shareUrl ?? ""}>
                <Clipboard.Label class="font-semibold">
                    {m["current-user.invitations.share"]()}:
                </Clipboard.Label>
                <Clipboard.Control
                    class="mt-2 flex items-center gap-1 rounded-lg border border-cream-950 bg-white p-2"
                >
                    {#if isLoading}
                        <Skeleton grow inline={false} />
                    {:else}
                        <Clipboard.ValueText class="truncate font-medium" />
                    {/if}
                    <Clipboard.Trigger
                        class="shrink-0 p-1"
                        disabled={isLoading}
                    >
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

    {#if !isNotFound}
        <ScreenView.Footer class="mt-5">
            <Confirmable
                bind:open={
                    () => deleteView.meta?.__deleteinvitation?.isOpen ?? false,
                    (v) => {
                        if (v) {
                            void deleteView.push(view.name, {
                                meta: {
                                    ...view.meta,
                                    __deleteinvitation: { isOpen: true }
                                }
                            });
                        } else {
                            void deleteView.back();
                        }
                    }
                }
                title={m["current-user.invitations.delete-ahtung"]()}
                onConfirm={() => {
                    if (!invitation) return;
                    confirmedIdToDelete = invitation.id;
                }}
                onExitComplete={async () => {
                    if (!confirmedIdToDelete) return;
                    await view.back();
                    deleteInvitationMutation.mutate(confirmedIdToDelete);
                    confirmedIdToDelete = null;
                }}
            >
                {#snippet trigger(props)}
                    <Button
                        {...props()}
                        type="button"
                        class="shrink-0"
                        disabled={!invitation}
                    >
                        {m["current-user.invitations.delete"]()}
                    </Button>
                {/snippet}
            </Confirmable>
        </ScreenView.Footer>
    {/if}
</ScreenView.Overlay>
