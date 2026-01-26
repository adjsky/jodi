<script lang="ts">
    import { Clipboard } from "@ark-ui/svelte";
    import { router } from "@inertiajs/svelte";
    import { CheckIcon, ClipboardCopyIcon } from "@lucide/svelte";
    import { destroy } from "$/generated/actions/App/Http/Controllers/RegistrationInvitationController";
    import { m } from "$/paraglide/messages";
    import { toaster } from "$/shared/lib/toaster";
    import Button from "$/shared/ui/Button.svelte";
    import Confirmable from "$/shared/ui/Confirmable.svelte";
    import FloatingView from "$/shared/ui/FloatingView.svelte";
    import { resource } from "runed";

    import { fetchInvitation, NotFoundResourceError } from "../api/invitations";
    import { buildViewName, view } from "../model/view";
    import { back } from "./Back.svelte";

    type Props = {
        onDelete?: (id: string) => void;
    };

    const { onDelete }: Props = $props();

    const id = $derived.by(() => {
        const [_, __, id] = view.name.split("/");
        return id;
    });

    const invitation = resource(() => id, fetchInvitation);

    $effect(() => {
        if (invitation.error) {
            if (invitation.error instanceof NotFoundResourceError) {
                void view.replace(buildViewName("invitations"));
            } else {
                toaster.error(m["common.unexpected-error"]());
            }
        }
    });
</script>

<FloatingView {back} title={m["current-user.invitations.invitation"]()}>
    {#if invitation.current}
        <div class="flex grow flex-col justify-between py-5">
            <!-- TODO: maybe use web share API? -->
            <Clipboard.Root value={invitation.current.shareUrl}>
                <Clipboard.Label class="font-semibold">
                    {m["current-user.invitations.share"]()}:
                </Clipboard.Label>
                <Clipboard.Control
                    class="mt-2 flex justify-between gap-1 rounded-lg border border-cream-950 bg-white p-2"
                >
                    <Clipboard.Input class="w-full font-medium outline-none" />
                    <Clipboard.Trigger class="p-1">
                        <Clipboard.Indicator>
                            {#snippet copied()}
                                <CheckIcon class="text-xl text-green" />
                            {/snippet}
                            <ClipboardCopyIcon class="text-xl " />
                        </Clipboard.Indicator>
                    </Clipboard.Trigger>
                </Clipboard.Control>
            </Clipboard.Root>

            <Confirmable
                title={m["current-user.invitations.delete-ahtung"]()}
                onConfirm={() => {
                    void router.visit(destroy(invitation.current!.id), {
                        replace: true,
                        preserveUrl: true,
                        preserveState: true,
                        only: ["flash", "me"],
                        onSuccess: () => {
                            void view.back();
                            onDelete?.(invitation.current!.id);
                        }
                    });
                }}
            >
                {#snippet children(props)}
                    <Button {...props()}>
                        {m["current-user.invitations.delete"]()}
                    </Button>
                {/snippet}
            </Confirmable>
        </div>
    {/if}
</FloatingView>
