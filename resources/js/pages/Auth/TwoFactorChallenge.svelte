<script>
    import { Form, page } from "@inertiajs/svelte";
    import { store } from "$actions/TwoFactorChallengeController";
</script>

{$page.props.flash.message}

<Form action={store()}>
    <input name="password" />

    <button type="submit">Submit</button>
</Form>
