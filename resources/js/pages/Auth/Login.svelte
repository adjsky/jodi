<script>
    import { Form, page } from "@inertiajs/svelte";
    import { Mail } from "@lucide/svelte";
    import TextField from "$/shared/ui/primitives/TextField.svelte";
    import { store } from "$actions/LoginController";
</script>

{$page.props.flash.message}

<Form action={store()} class="px-4">
    <TextField type="email" name="email" placeholder="Enter your email">
        {#snippet indicator()}<Mail />{/snippet}
    </TextField>

    <button type="submit">Login</button>
</Form>
