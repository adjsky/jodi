<script>
    import { Form, page } from "@inertiajs/svelte";
    import { Mail } from "@lucide/svelte";
    import Input from "$/shared/ui/primitives/Input.svelte";
    import { store } from "$actions/LoginController";
</script>

{$page.props.flash.message}

<Form action={store()} class="px-4">
    <Input type="email" name="email" placeholder="Enter your email">
        {#snippet indicator()}<Mail />{/snippet}
    </Input>

    <button type="submit">Login</button>
</Form>
