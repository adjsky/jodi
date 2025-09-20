<script>
    import { Form } from "@inertiajs/svelte";
    import { store } from "$/actions/App/Http/Controllers/LoginController";
</script>

<Form action={store()}>
    <input type="email" name="email" />

    <button type="submit">Login</button>
</Form>
