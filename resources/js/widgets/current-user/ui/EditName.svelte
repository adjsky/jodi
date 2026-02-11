<script lang="ts">
    import { Form, page } from "@inertiajs/svelte";
    import { AtSign } from "@lucide/svelte";
    import { update } from "$/generated/actions/App/Http/Controllers/CurrentUserController";
    import { m } from "$/paraglide/messages";
    import Button from "$/shared/ui/Button.svelte";
    import FloatingView from "$/shared/ui/FloatingView.svelte";
    import TextField from "$/shared/ui/TextField.svelte";

    import { view } from "../model/view";
    import { back } from "./Back.svelte";

    const user = $derived($page.props.auth.user);
</script>

<FloatingView {back} title={m["current-user.account.name"]()}>
    <Form
        action={update()}
        class="flex grow flex-col justify-between py-5"
        options={{ replace: true, preserveUrl: true, only: ["auth"] }}
        onSuccess={() => view.back()}
        let:processing
        let:errors
        let:isDirty
    >
        <TextField
            type="text"
            name="name"
            placeholder={m["current-user.account.name"]()}
            error={errors.name}
            defaultValue={user.name}
            required
        >
            {#snippet indicator()}<AtSign />{/snippet}
        </TextField>

        <Button type="submit" disabled={processing || !isDirty}>
            {m["current-user.name.save"]()}
        </Button>
    </Form>
</FloatingView>
