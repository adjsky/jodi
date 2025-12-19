<script lang="ts">
    import { Combobox, useFilter, useListCollection } from "@ark-ui/svelte";
    import { page } from "@inertiajs/svelte";
    import { CirclePlus, Tag, Trash } from "@lucide/svelte";
    import { m } from "$/paraglide/messages";
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
    // svelte-ignore state_referenced_locally
    let categoryInputValue = $state(selected ?? "");

    const filters = useFilter({ sensitivity: "base" });

    const autoresize = useAutoresize();

    const { collection, filter, set } = useListCollection({
        initialItems: [],
        filter(itemString, filterText) {
            return filters().contains(itemString, filterText);
        }
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

    async function ontrigger(e: Event) {
        e.stopPropagation();
        open = true;
        await tick();
        categoryInput?.focus();
    }

    $effect(() => {
        const categories = $page.props["categories"];
        if (categories) set(categories);
    });
</script>

<input
    bind:this={formInput}
    type="text"
    defaultValue={selected ?? ""}
    {name}
    hidden
/>

<Combobox.Root
    {collection}
    allowCustomValue
    bind:open
    bind:inputValue={categoryInputValue}
    bind:value={() => (selected ? [selected] : []), ([v]) => (selected = v)}
    onInputValueChange={(d) => oninput(d.inputValue)}
    onSelect={(d) => onselect(d.itemValue)}
    positioning={{ sameWidth: false, placement: "bottom-start" }}
>
    <Combobox.Control>
        {#if open}
            <Combobox.Input
                bind:ref={categoryInput}
                {@attach autoresize}
                class={["min-w-10 px-2.5 py-0.5 font-bold outline-none"]}
            />
        {:else}
            <button
                type="button"
                class={[
                    "flex items-center gap-1 rounded-full px-2.5 py-0.5 font-bold",
                    selected && "bg-peach",
                    !selected && "outline outline-cream-400 outline-dashed"
                ]}
                onclick={ontrigger}
            >
                {#if selected}
                    <Tag class="text-sm" />
                    {selected}
                {:else}
                    + {m["todos.category"]()}
                {/if}
            </button>
        {/if}
    </Combobox.Control>
    <Combobox.Positioner>
        <Combobox.Content
            class="min-w-40 rounded-xl bg-white px-3 py-2 font-bold outline outline-cream-950"
            onclick={(e) => e.stopPropagation()}
        >
            {#if collection().size != 0}
                <Combobox.ItemGroup class="max-h-45 overflow-scroll">
                    {#each collection().items as item (item)}
                        <Combobox.Item
                            {item}
                            class="flex cursor-pointer items-center justify-between rounded-lg px-3 py-2 data-[state=checked]:bg-peach"
                        >
                            <Combobox.ItemText>{item}</Combobox.ItemText>
                            <Combobox.ItemIndicator>✓</Combobox.ItemIndicator>
                        </Combobox.Item>
                    {/each}
                </Combobox.ItemGroup>
            {/if}
            <Combobox.ItemGroup>
                {#if categoryInputValue != "" && categoryInputValue != selected && !collection().has(categoryInputValue)}
                    <Combobox.Item
                        item={categoryInputValue}
                        class="cursor-pointer px-1 py-2"
                    >
                        <Combobox.ItemText class="flex items-center gap-1">
                            <CirclePlus />
                            {m["todos.create-new-category"]()}
                        </Combobox.ItemText>
                    </Combobox.Item>
                {/if}
                {#if categoryInputValue != ""}
                    <Combobox.Item item="" class="cursor-pointer px-1 py-2">
                        <Combobox.ItemText
                            class="flex items-center gap-1 text-red"
                        >
                            <Trash />
                            {m["todos.reset-category"]()}
                        </Combobox.ItemText>
                    </Combobox.Item>
                {/if}
            </Combobox.ItemGroup>
        </Combobox.Content>
    </Combobox.Positioner>
</Combobox.Root>
