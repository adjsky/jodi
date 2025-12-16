<script lang="ts">
    import { Clipboard } from "@ark-ui/svelte";
    import { router } from "@inertiajs/svelte";
    import { CheckIcon, ClipboardCopyIcon } from "@lucide/svelte";
    import SettingsLayout from "$/app/ui/layouts/SettingLayout.svelte";
    import { destroy } from "$/generated/actions/App/Http/Controllers/RegistrationInvitationController";
    import { invitations } from "$/generated/routes";
    import { m } from "$/paraglide/messages";
    import Button from "$/shared/ui/Button.svelte";
    import Confirmable from "$/shared/ui/Confirmable.svelte";

    type Props = {
        invitation: App.Data.RegistrationInvitationDto;
        shareUrl: string;
    };

    const { invitation, shareUrl }: Props = $props();
</script>

<SettingsLayout
    backHref={invitations()}
    title={m["current-user.invitations.invitation"]()}
>
    <div class="flex flex-grow flex-col justify-between py-5">
        <!-- TODO: maybe use web share API? -->
        <Clipboard.Root value={shareUrl}>
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
            onconfirm={() => {
                router.visit(destroy(invitation.id), {
                    viewTransition: true
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
</SettingsLayout>
