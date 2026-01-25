<script lang="ts">
    import { Combobox, useFilter, useListCollection } from "@ark-ui/svelte";
    import { page, router } from "@inertiajs/svelte";
    import { Delete, Tag, Trash } from "@lucide/svelte";
    import { destroy } from "$/generated/actions/App/Http/Controllers/CategoryController";
    import { m } from "$/paraglide/messages";
    import { optimistic } from "$/shared/inertia/visit/optimistic";
    import Confirmable from "$/shared/ui/Confirmable.svelte";
    import { tick } from "svelte";

    import { useAutoresize } from "../helpers/autoresize.svelte";

    type Props = {
        name: string;
        defaultValue?: string | null;
    };

    const { name, defaultValue }: Props = $props();

    let selected = $derived(defaultValue);

    let formInput = $state<HTMLInputElement | null>(null);
    let categoryInput = $state<HTMLInputElement | null>(null);

    let open = $state(false);
    let categoryToDelete = $state<string | null>(null);
    // svelte-ignore state_referenced_locally
    let categoryInputValue = $state(selected ?? "");

    const autoresize = useAutoresize();

    const filters = useFilter({ sensitivity: "base" });
    const { collection, filter, set } = useListCollection({
        initialItems: [] as string[],
        filter(itemString, filterText) {
            return filters().contains(itemString, filterText);
        }
    });

    $effect(() => {
        const categories = $page.props["categories"];
        if (categories) set(categories);
    });

    function oninput(value: string) {
        filter(value);
        autoresize.update(value);
    }

    function onselect(value: string) {
        if (!formInput) {
            return;
        }

        formInput.value = value;
        formInput.dispatchEvent(new Event("input", { bubbles: true }));
    }

    function add() {
        selected = categoryInputValue;
        onselect(categoryInputValue);
    }

    function onkeydown(e: KeyboardEvent) {
        if (e.key != "Enter") return;
        add();
    }

    async function ontrigger(e: Event) {
        e.stopPropagation();
        open = true;
        await tick();
        categoryInput?.focus();
    }

    const showReset = $derived(categoryInputValue != "");
    const showCategories = $derived(collection().size != 0);
    const showContent = $derived(showReset || showCategories);
</script>

<Combobox.Root
    {collection}
    {onkeydown}
    allowCustomValue
    bind:open
    bind:inputValue={categoryInputValue}
    bind:value={() => (selected ? [selected] : []), ([v]) => (selected = v)}
    onInputValueChange={(d) => oninput(d.inputValue)}
    onSelect={(d) => onselect(d.itemValue)}
    onFocusOutside={add}
    positioning={{ sameWidth: false, placement: "bottom-start" }}
>
    <Combobox.Control>
        {#if open}
            <Combobox.Input
                bind:ref={categoryInput}
                {@attach autoresize}
                class="rounded-full px-2.5 py-0.5 font-bold outline outline-offset-0 outline-cream-400 outline-dashed placeholder:text-cream-700"
                placeholder="+ {m['todos.category.placeholder']()}"
            />
        {:else}
            <button
                type="button"
                class={[
                    "flex items-center gap-1 rounded-full px-2.5 py-0.5 font-bold",
                    selected && "bg-peach",
                    !selected &&
                        "text-cream-700 outline outline-cream-400 outline-dashed"
                ]}
                onclick={ontrigger}
            >
                {#if selected}
                    <Tag class="text-sm" />
                    {selected}
                {:else}
                    + {m["todos.category.placeholder"]()}
                {/if}
            </button>
        {/if}
    </Combobox.Control>
    <Combobox.Positioner>
        <Combobox.Content
            class={[
                "min-w-40 rounded-xl bg-white px-3 py-2 font-bold outline outline-cream-950",
                !showContent && "invisible"
            ]}
            onclick={(e) => e.stopPropagation()}
        >
            {#if showCategories}
                <Combobox.ItemGroup class="max-h-45 overflow-scroll">
                    {#each collection().items as item (item)}
                        <Combobox.Item
                            {item}
                            class="group flex cursor-pointer items-center justify-between rounded-lg px-3 py-2 data-[state=checked]:bg-peach"
                        >
                            <Combobox.ItemText>{item}</Combobox.ItemText>
                            <button
                                onclick={(e) => {
                                    e.stopPropagation();
                                    categoryToDelete = item;
                                }}
                                type="button"
                                class="group-data-[state=checked]:hidden"
                            >
                                <Trash class="text-red" />
                            </button>
                            <Combobox.ItemIndicator>✓</Combobox.ItemIndicator>
                        </Combobox.Item>
                    {/each}
                </Combobox.ItemGroup>
            {/if}
            <Combobox.ItemGroup>
                {#if showReset}
                    <Combobox.Item item="" class="cursor-pointer px-1 py-2">
                        <Combobox.ItemText
                            class="flex items-center gap-1 text-red"
                        >
                            <Delete class="text-xl" />
                            {m["todos.category.reset"]()}
                        </Combobox.ItemText>
                    </Combobox.Item>
                {/if}
            </Combobox.ItemGroup>
        </Combobox.Content>
    </Combobox.Positioner>
</Combobox.Root>

<Confirmable
    open={categoryToDelete != null}
    title={m["todos.category.confirm-delete"]({
        category: categoryToDelete ?? "<null>"
    })}
    onConfirm={async () => {
        if (!categoryToDelete) return;

        void router.visit(destroy(categoryToDelete), {
            ...optimistic(
                (prev) => ({
                    todos: prev.todos.map((t: App.Data.TodoDto) => ({
                        ...t,
                        category:
                            t.category == categoryToDelete ? null : t.category
                    })),
                    categories: prev.categories.filter(
                        (c: string) => c != categoryToDelete
                    )
                }),
                {
                    error: m["todos.errors.category"]()
                }
            ),
            only: ["todos", "categories"],
            preserveState: true,
            preserveScroll: true,
            preserveUrl: true,
            replace: true,
            showProgress: false
        });

        categoryToDelete = null;

        return true;
    }}
    onOpenChange={(_open) => {
        if (_open) return;
        open = false;
        categoryToDelete = null;
    }}
/>

<input
    bind:this={formInput}
    type="text"
    defaultValue={selected ?? ""}
    {name}
    hidden
/>
