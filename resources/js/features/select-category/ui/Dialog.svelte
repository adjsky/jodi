<script lang="ts">
    import { Dialog, useFilter, useListCollection } from "@ark-ui/svelte";
    import { page } from "@inertiajs/svelte";
    import { ChevronLeft, Search, Tag, X } from "@lucide/svelte";
    import { m } from "$/paraglide/messages";
    import Jelly from "$/shared/assets/jelly.svg";
    import { announce } from "$/shared/lib/form";
    import { tick } from "svelte";

    import { view } from "../model/view";
    import AddCategory from "./AddCategory.svelte";
    import Categories from "./Categories.svelte";
    import DeleteCategory from "./DeleteCategory.svelte";

    type Props = {
        name: string;
        current: string | null;
    };

    let { name, ...props }: Props = $props();

    const filters = useFilter({ sensitivity: "base" });
    const { collection, filter, set } = useListCollection({
        initialItems: [] as string[],
        filter(itemString, filterText) {
            return filters().contains(itemString, filterText);
        }
    });

    let formInput = $state<HTMLInputElement | null>(null);
    let selected = $state(props.current);
    let search = $state("");

    let showAddButton = $derived(search != "" && !collection().has(search));

    async function onSelect(name: string | null) {
        selected = name;
        await tick();
        announce(formInput);
    }

    $effect(() => {
        const categories = $page.props["categories"];
        if (categories) set(categories);
    });

    $effect(() => {
        filter(search);
    });
</script>

<input bind:this={formInput} type="text" value={selected} {name} hidden />

<Dialog.Root
    bind:open={
        () => view.meta?.__selectcategory?.isOpen ?? false,
        (v) => {
            if (v) {
                void view.push(view.name, {
                    ...view.meta,
                    __selectcategory: { isOpen: true }
                });
            } else {
                void view.back();
            }
        }
    }
    onExitComplete={() => {
        search = "";
    }}
>
    <Dialog.Trigger
        class={[
            "flex items-center gap-1 rounded-full px-2.5 py-0.5 font-bold",
            selected && "bg-peach",
            !selected &&
                "text-cream-700 outline outline-cream-400 outline-dashed"
        ]}
    >
        {#if selected}
            <Tag class="text-sm" />
            {selected}
        {:else}
            {m["todos.category.trigger"]()}
        {/if}
    </Dialog.Trigger>
    <Dialog.Backdrop
        class={[
            "fixed inset-0 z-100 bg-cream-950/60 duration-300",
            "data-[state=closed]:animate-out data-[state=closed]:fade-out",
            "data-[state=open]:animate-in data-[state=open]:fade-in"
        ]}
    />
    <Dialog.Positioner>
        <Dialog.Content
            class={[
                "fixed inset-x-0 bottom-0 z-100 flex h-[75%] flex-col rounded-t-2xl bg-white px-4 py-3 duration-300",
                "data-[state=closed]:animate-out data-[state=closed]:slide-out-to-bottom",
                "data-[state=open]:animate-in data-[state=open]:slide-in-from-bottom"
            ]}
        >
            <DeleteCategory
                onDelete={(name) => {
                    if (name == selected) {
                        void onSelect(null);
                    }
                }}
            />

            <div class="relative flex items-center justify-between">
                <button class="p-2" type="button" onclick={() => view.back()}>
                    <ChevronLeft class="text-4xl" />
                </button>
                <span
                    class="absolute top-1/2 left-1/2 -translate-1/2 text-xl font-bold"
                >
                    {m["todos.category.title"]()}
                </span>
            </div>

            <div class="relative mt-3 flex items-center">
                <Search
                    class="pointer-events-none absolute left-3 text-xl text-cream-600"
                />

                {#if search != ""}
                    <button
                        type="button"
                        class="absolute right-0 p-3"
                        onclick={() => (search = "")}
                    >
                        <X class="text-2xl" />
                    </button>
                {/if}

                <input
                    bind:value={search}
                    class="form-input h-13.75 w-full rounded-xl border-none bg-cream-500/10 pl-10 text-lg font-medium outline-none placeholder:text-cream-600 focus:ring-0"
                    placeholder={m["todos.category.placeholder"]()}
                />
            </div>

            <div class="mt-2 flex grow flex-col overflow-y-scroll">
                {#if showAddButton}
                    <AddCategory
                        name={search}
                        onAdd={async () => {
                            void onSelect(search);
                            search = "";
                        }}
                    />
                {/if}

                {#if showAddButton && collection().size > 0}
                    <hr class="mt-2 text-cream-300" />
                {/if}

                {#if search == "" && collection().size == 0}
                    <img
                        src={Jelly}
                        width={82}
                        height={85}
                        alt=""
                        loading="lazy"
                        decoding="async"
                        class="mx-auto mt-14 w-full max-w-28"
                    />
                    <p
                        class="mx-auto mt-8 max-w-74 text-center text-lg font-medium"
                    >
                        {m["todos.category.no-categories"]()}
                    </p>
                {/if}

                <Categories
                    {selected}
                    list={collection().items}
                    onSelect={(name) => {
                        void onSelect(name);
                    }}
                />
            </div>
        </Dialog.Content>
    </Dialog.Positioner>
</Dialog.Root>
