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
        deleteInvitationMutationOptions,
        invitationsQueryOptions
    } from "../api/invitations";

    const id = $derived(Overlay.meta?.invitation?.id);
    let confirmedIdToDelete: string | null = null;

    const invitations = createQuery(() => ({
        ...invitationsQueryOptions,
        enabled: Boolean(id)
    }));

    const deleteInvitationMutation = createMutation(
        () => deleteInvitationMutationOptions
    );

    const invitation = $derived(
        invitations.data?.find((invitation) => invitation.id === id)
    );

    const isError = $derived(!invitations.data && invitations.error);
    const isLoading = $derived(invitations.isLoading);
    const isNotFound = $derived(invitations.data && !invitation);
</script>

<ScreenView.Overlay bind:open={() => Boolean(id), () => Overlay.close()}>
    <ScreenView.Header title={m["current-user.invitations.invitation"]()} />
    <ScreenView.Content
        class={[isError || isNotFound ? "justify-center" : "mt-5"]}
        aria-live="polite"
        aria-busy={invitations.isFetching}
    >
        {#if isError}
            <ResourceError
                message={m["current-user.invitations.errors.list"]()}
                onRetry={() => invitations.refetch()}
            />
        {:else if isNotFound}
            <ResourceError
                message={m["current-user.invitations.errors.not-found"]()}
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
            <Button
                type="button"
                class="shrink-0"
                disabled={!invitation}
                onclick={() =>
                    Overlay.open(
                        "me/invitations",
                        {
                            ...Overlay.meta,
                            deleteinvitation: { isOpen: true }
                        },
                        { skipViewTransition: true }
                    )}
            >
                {m["current-user.invitations.delete"]()}
            </Button>
        </ScreenView.Footer>
    {/if}
</ScreenView.Overlay>

<Confirmable
    bind:open={
        () => Overlay.meta?.deleteinvitation?.isOpen ?? false,
        () => Overlay.close()
    }
    title={m["current-user.invitations.delete-ahtung"]()}
    onConfirm={async () => {
        if (!invitation) raise("No invitation available.");
        confirmedIdToDelete = invitation.id;
        void Overlay.close();
    }}
    onExitComplete={async () => {
        if (!confirmedIdToDelete) return;
        const id = confirmedIdToDelete;
        confirmedIdToDelete = null;
        await Overlay.close();
        deleteInvitationMutation.mutate(id);
    }}
/>
