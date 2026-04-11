<script lang="ts">
    import { Form } from "@inertiajs/svelte";
    import { Plus } from "@lucide/svelte";
    import CreateCategory from "$/generated/actions/App/Domain/Todo/Actions/CreateCategory";
    import { m } from "$/paraglide/messages";

    type Props = {
        name: string;
        onAdd?: (id: number) => void;
    };

    const { name, onAdd }: Props = $props();
</script>

<Form
    action={CreateCategory()}
    options={{
        only: ["flash", "categories"],
        preserveState: true,
        preserveScroll: true,
        preserveUrl: true,
        replace: true
    }}
    onSuccess={(page) => onAdd?.(page.props.flash.id)}
    let:processing
>
    <input hidden name="name" value={name} />
    <button
        type="submit"
        disabled={processing}
        class="flex h-13.75 w-full items-center gap-2 rounded-xl bg-brand/10 px-2 text-start text-lg font-medium"
    >
        <span
            class="flex size-7 items-center justify-center rounded-full bg-brand"
        >
            <Plus class="text-xl text-white" />
        </span>
        {m["todos.category.add"]()}
    </button>
</Form>
