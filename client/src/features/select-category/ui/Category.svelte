<script lang="ts">
    import { Plus, Search, Tag, X } from "@lucide/svelte";
    import { CategoryRepository } from "#entities/todo/mod.ts";
    import { m } from "#paraglide/messages.js";
    import Jelly from "#shared/assets/jelly.svg";
    import Confirmable from "#shared/ui/Confirmable.svelte";
    import SheetDialog from "#shared/ui/SheetDialog.svelte";

    import List from "./List.svelte";

    type Props = {
        category_id: string | null;
    };

    let { category_id: current = $bindable() }: Props = $props();

    let search = $state("");
    let open = $state(false);
    let confirm = $state(false);
    let confirm_id: string | null = $state(null);

    const cquery = CategoryRepository.list();

    const categories = $derived(cquery.current ?? []);
    const filtered = $derived(categories.filter((c) => filter(c.name)));

    const show_add_button = $derived(
        search !== "" && filtered.findIndex((c) => filter(c.name)) === -1
    );
    const has_no_categories = $derived(search === "" && filtered.length === 0);

    function filter(name: string) {
        return name.toLowerCase().includes(search);
    }
</script>

<SheetDialog
    bind:open
    portal
    lazy_mount
    class="pb-0"
    height={75}
    title={m["todos.category.title"]()}
    on_exit_complete={() => {
        search = "";
    }}
>
    {#snippet trigger(props)}
        <button
            {...props()}
            class={[
                "w-fit max-w-full truncate rounded-full px-2.5 py-0.5 font-bold",
                current && "bg-peach",
                !current && "text-cream-700 outline outline-cream-400 outline-dashed"
            ]}
        >
            {#if current}
                <Tag class="inline text-sm" />
                {categories.find((c) => c.id === current)?.name}
            {:else}
                {m["todos.category.trigger"]()}
            {/if}
        </button>
    {/snippet}

    <div class="relative mt-3 flex items-center">
        <Search class="pointer-events-none absolute left-3 text-xl text-cream-600" />

        {#if search !== ""}
            <button type="button" class="absolute right-0 p-3" onclick={() => (search = "")}>
                <X class="text-2xl" />
            </button>
        {/if}

        <input
            bind:value={search}
            class="form-input h-14 w-full rounded-xl border-none bg-cream-500/10 ps-10 pe-12 text-lg font-medium outline-none placeholder:text-cream-600 focus:ring-0"
            placeholder={m["todos.category.placeholder"]()}
            maxlength={50}
        />
    </div>

    <div class={["flex min-h-0 grow flex-col", !has_no_categories && "mt-2"]}>
        {#if show_add_button}
            <button
                type="button"
                class="flex h-14 w-full items-center gap-2 rounded-xl bg-brand/10 px-2 text-start text-lg font-medium disabled:cursor-not-allowed"
                onclick={async () => {
                    const id = await CategoryRepository.create(search);
                    search = "";
                    current = id;
                }}
            >
                <span class="flex size-7 items-center justify-center rounded-full bg-brand">
                    <Plus class="text-xl text-white" />
                </span>
                {m["todos.category.add"]()}
            </button>
        {/if}

        {#if show_add_button && filtered.length > 0}
            <hr class="mt-2 text-cream-300" />
        {/if}

        {#if has_no_categories}
            <img
                src={Jelly}
                width={82}
                height={85}
                alt=""
                decoding="async"
                class="mx-auto mt-[10vh] w-full max-w-28"
            />
            <p class="mx-auto mt-8 max-w-74 text-center text-lg font-medium">
                {m["todos.category.no-categories"]()}
            </p>
        {/if}

        <List
            {current}
            list={filtered}
            on_select={(id) => (current = id)}
            on_delete={(id) => {
                confirm = true;
                confirm_id = id;
            }}
        />
    </div>

    <Confirmable
        bind:open={confirm}
        title={m["todos.category.confirm-delete"]({
            category: categories.find((c) => c.id === confirm_id)?.name ?? ""
        })}
        on_confirm={async () => {
            if (!confirm_id) return;
            await CategoryRepository.destroy(confirm_id);
            if (confirm_id === current) current = null;
            confirm = false;
        }}
        on_exit_complete={() => {
            confirm_id = null;
        }}
    />
</SheetDialog>
