<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import { AtSign } from "@lucide/svelte";
    import Intro from "$/app/ui/auth/Intro.svelte";
    import AuthLayout from "$/app/ui/layouts/AuthLayout.svelte";
    import { signup } from "$/generated/actions/App/Http/Controllers/SignupController";
    import { m } from "$/paraglide/messages";
    import Bee from "$/shared/assets/bee.svg";
    import Calendar from "$/shared/assets/calendar.svg";
    import Cat from "$/shared/assets/cat.svg";
    import { HistoryView } from "$/shared/inertia/history-view.svelte";
    import Button from "$/shared/ui/Button.svelte";
    import TextField from "$/shared/ui/TextField.svelte";

    const { code }: { code: string } = $props();

    const view = new HistoryView();
</script>

<AuthLayout>
    {#if view.isOpen("start")}
        <Intro class="mt-35" title={m["signup.step.start.title"]()}>
            {#snippet icon()}
                <img src={Bee} width={92} height={85} alt="" />
            {/snippet}
        </Intro>
        <Form
            action={signup(code)}
            class="mt-13 space-y-4"
            let:processing
            let:errors
        >
            <TextField
                type="text"
                name="name"
                placeholder={m["signup.step.start.name-placeholder"]()}
                error={errors.name}
                required
            >
                {#snippet indicator()}<AtSign />{/snippet}
            </TextField>
            <Button type="submit" disabled={processing}>
                {m["signup.step.start.submit"]()}
            </Button>
        </Form>
    {:else}
        <Intro class="mt-16" title={m["signup.intro.title"]()}>
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
        <Button
            class="fixed inset-x-4 bottom-12 w-auto"
            onclick={() => view.push("start")}
        >
            {m["signup.intro.start"]()}
        </Button>
    {/if}
</AuthLayout>
