<script lang="ts">
    import { Combobox, useFilter, useListCollection } from "@ark-ui/svelte";
    import { page } from "@inertiajs/svelte";
    import { Delete, Tag, Trash } from "@lucide/svelte";
    import { m } from "$/paraglide/messages";
    import { announce } from "$/shared/lib/form";
    import { tick } from "svelte";

    import { useAutoresize } from "../helpers/autoresize.svelte";
    import { view } from "../model/view";
    import DeleteCategory from "./DeleteCategory.svelte";

    type Props = {
        name: string;
        defaultValue?: string | null;
    };

    const { name, defaultValue }: Props = $props();

    let selected = $derived(defaultValue);

    let formInput = $state<HTMLInputElement | null>(null);
    let categoryInput = $state<HTMLInputElement | null>(null);

    let open = $state(false);
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
        categoryInputValue = value;
    }

    async function onchange(value: string, isNew?: boolean) {
        selected = value;
        await tick();
        announce(formInput);
        if (isNew) oninput(value);
    }

    function add() {
        if (categoryInputValue == selected) return;
        void onchange(categoryInputValue, true);
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

<DeleteCategory />

<input bind:this={formInput} type="text" value={selected} {name} hidden />

<Combobox.Root
    bind:open
    {collection}
    {onkeydown}
    allowCustomValue
    inputValue={categoryInputValue}
    onInputValueChange={(d) => oninput(d.inputValue)}
    value={selected ? [selected] : []}
    onValueChange={({ value: [value] }) => onchange(value)}
    onInteractOutside={() => add()}
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
                "z-10 min-w-40 rounded-xl bg-white px-3 py-2 font-bold outline outline-cream-950",
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
                                onclick={async (e) => {
                                    e.stopPropagation();
                                    open = false;
                                    await tick();
                                    void view.push(view.name, {
                                        ...view.meta,
                                        __categorytodelete: item
                                    });
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
