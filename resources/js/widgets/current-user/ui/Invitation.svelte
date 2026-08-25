<script lang="ts">
    import { Clipboard } from "@ark-ui/svelte";
    import { router } from "@inertiajs/svelte";
    import { CheckIcon, ClipboardCopyIcon } from "@lucide/svelte";
    import DestroyRegistrationInvitation from "$/generated/actions/App/Domain/Identity/Actions/DestroyRegistrationInvitation";
    import { m } from "$/paraglide/messages";
    import { ScreenView } from "$/shared/composites/screen-view";
    import { HistoryView } from "$/shared/integrations/inertia";
    import Button from "$/shared/ui/Button.svelte";
    import Confirmable from "$/shared/ui/Confirmable.svelte";

    import { view } from "../model/view";
    import { back } from "./Back.svelte";

    import type { RegistrationInvitationData } from "$/entities/user";
    import type { ResourceReturn } from "runed";

    type Props = {
        resource: ResourceReturn<RegistrationInvitationData[]>;
        onDelete?: (id: string) => void;
    };

    const { resource, onDelete }: Props = $props();

    const deleteView = new HistoryView<{
        __deleteinvitation: { isOpen: boolean };
    }>();

    let isDeleting = $state(false);

    const id = $derived.by(() => {
        const [_, __, id] = view.name.split("/");
        return id;
    });

    const invitation = $derived(
        resource.current?.find((invitation) => invitation.id == id)
    );

    let isError = $derived(!resource.current && resource.error);
    let isLoading = $derived(!resource.current && resource.loading);
    let isNotFound = $derived(resource.current && !invitation);
</script>

<ScreenView.Overlay>
    <ScreenView.Header
        {back}
        title={m["current-user.invitations.invitation"]()}
    />
    <ScreenView.Content
        class={["mt-5", (isError || isNotFound) && "justify-center"]}
        aria-live="polite"
        aria-busy={resource.loading}
    >
        {#if isError || isNotFound}
            error
        {:else if isLoading}
            loading...
        {:else if invitation}
            <!-- TODO: maybe use web share API? -->
            <Clipboard.Root value={invitation.shareUrl}>
                <Clipboard.Label class="font-semibold">
                    {m["current-user.invitations.share"]()}:
                </Clipboard.Label>
                <Clipboard.Control
                    class="mt-2 flex items-center gap-1 rounded-lg border border-cream-950 bg-white p-2"
                >
                    <Clipboard.ValueText class="truncate font-medium" />
                    <Clipboard.Trigger class="shrink-0 p-1">
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
            onConfirm={async () => {
                if (!invitation || isDeleting) return;

                isDeleting = true;

                try {
                    await router.visit(
                        DestroyRegistrationInvitation(invitation.id),
                        {
                            replace: true,
                            preserveUrl: true,
                            preserveState: true,
                            only: ["flash", "me"],
                            onSuccess: () => {
                                void view.back();
                                onDelete?.(invitation.id);
                            }
                        }
                    );

                    return true;
                } finally {
                    isDeleting = false;
                }
            }}
        >
            {#snippet trigger(props)}
                <Button
                    {...props()}
                    type="button"
                    class="shrink-0"
                    disabled={!invitation || isDeleting}
                >
                    {m["current-user.invitations.delete"]()}
                </Button>
            {/snippet}
        </Confirmable>
    </ScreenView.Footer>
</ScreenView.Overlay>
