<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import { User } from "@lucide/svelte";
    import { update } from "$/generated/actions/App/Http/Controllers/CurrentUserController";
    import { m } from "$/paraglide/messages";
    import { toastify } from "$/shared/inertia/visit/toastify";
    import Button from "$/shared/ui/Button.svelte";
    import TextField from "$/shared/ui/TextField.svelte";

    import Layout from "./Layout.svelte";

    import type { LayoutProps } from "../model/types";

    type Props = LayoutProps & {
        name: string;
    };

    const { name, ...props }: Props = $props();
</script>

<Layout {...props}>
    <Form
        {...toastify()}
        action={update()}
        class="flex h-full flex-col justify-between"
        let:processing
        let:errors
    >
        <TextField
            type="text"
            name="name"
            placeholder={m["current-user.account.name"]()}
            error={errors.name}
            defaultValue={name}
            required
        >
            {#snippet indicator()}<User />{/snippet}
        </TextField>

        <Button type="submit" disabled={processing}>
            {m["current-user.save"]()}
        </Button>
    </Form>
</Layout>
