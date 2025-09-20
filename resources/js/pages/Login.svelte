<script>
    import { Form, page } from "@inertiajs/svelte";
    import { store } from "$/actions/App/Http/Controllers/LoginController";
</script>

{$page.props.flash.message}

<Form action={store()}>
    <input type="email" name="email" />

    <button type="submit">Login</button>
</Form>
