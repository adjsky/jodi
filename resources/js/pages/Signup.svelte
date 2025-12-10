<script lang="ts">
    import Intro from "$/app/ui/auth/Intro.svelte";
    import AuthLayout from "$/app/ui/layouts/AuthLayout.svelte";
    import { m } from "$/paraglide/messages";
    import Calendar from "$/shared/assets/calendar.svg";
    import Cat from "$/shared/assets/cat.svg";
    import Button from "$/shared/ui/Button.svelte";
</script>

<AuthLayout>
    <Intro class="mt-16" title={m["signup.title"]()}>
        {#snippet icon()}
            <img src={Cat} width={94} height={85} alt="" />
        {/snippet}
    </Intro>
    <img
        class="mx-auto mt-10"
        src={Calendar}
        width={325}
        height={236}
        loading="lazy"
        decoding="async"
        alt=""
    />
    <Button class="fixed inset-x-4 bottom-12 w-auto">
        {m["signup.start"]()}
    </Button>
</AuthLayout>
