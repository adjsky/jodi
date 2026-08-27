<script lang="ts">
    import { useFilter, useListCollection } from "@ark-ui/svelte";
    import { Search, Tag, X } from "@lucide/svelte";
    import { createQuery, useQueryClient } from "@tanstack/svelte-query";
    import { m } from "$/paraglide/messages";
    import Jelly from "$/shared/assets/jelly.svg";
    import { dispatchInput } from "$/shared/lib/dom/dispatch-input";
    import { DeferUntilNextFrame } from "$/shared/lib/svelte/defer-until-next-frame.svelte";
    import ResourceError from "$/shared/ui/ResourceError.svelte";
    import SheetDialog from "$/shared/ui/SheetDialog.svelte";
    import { tick, untrack } from "svelte";

    import { categoriesQueryOptions } from "../api/categories";
    import { view } from "../model/view";
    import AddCategory from "./AddCategory.svelte";
    import Categories from "./Categories.svelte";
    import DeleteCategory from "./DeleteCategory.svelte";

    import type { CategoryData } from "$/entities/todo";

    type Props = {
        name: string;
        current: CategoryData | null;
        deferHistoryViewFrames?: number;
    };

    let { name, current, deferHistoryViewFrames = 0 }: Props = $props();

    const deferredView = new DeferUntilNextFrame(() => deferHistoryViewFrames);

    // ------------------------------ COLLECTION -------------------------------

    const { collection, filter, set } = useListCollection({
        initialItems: [] as { id: number; name: string }[],
        filter(itemString, filterText) {
            return filters().contains(itemString, filterText);
        },
        itemToString({ name }) {
            return name;
        },
        itemToValue({ name }) {
            return name;
        }
    });

    // ------------------------------- FILTERS ---------------------------------

    let search = $state("");

    const filters = useFilter({ sensitivity: "base" });

    $effect(() => {
        filter(search);
    });

    // --------------------------------- QUERY ---------------------------------

    const queryClient = useQueryClient();

    const categories = createQuery(() => categoriesQueryOptions);

    const isError = $derived(!categories.data && categories.error);
    const isLoading = $derived(categories.isLoading);

    $effect(() => {
        if (categories.data) {
            set(categories.data);
        }
    });

    // -------------------------------- STATES ---------------------------------

    let formInput = $state<HTMLInputElement | null>(null);
    let selected = $state(untrack(() => current));

    const showAddButton = $derived(search != "" && !collection().has(search));
    const hasNoCategories = $derived(search == "" && collection().size == 0);

    // ------------------------------- CALLBACKS -------------------------------

    async function onSelect(category: CategoryData | null) {
        selected = category;
        await tick();
        dispatchInput(formInput);
    }
</script>

<input bind:this={formInput} type="number" value={selected?.id} {name} hidden />

<SheetDialog
    bind:open={
        () =>
            deferredView.ready &&
            (view.meta?.__selectcategory?.isOpen ?? false),
        (v) => {
            if (v) {
                void view.push(view.name, {
                    meta: {
                        ...view.meta,
                        __selectcategory: { isOpen: true }
                    }
                });
            } else {
                void view.back();
            }
        }
    }
    class="pb-0"
    height={75}
    title={m["todos.category.title"]()}
    onExitComplete={() => {
        search = "";
    }}
    portal
    lazyMount
>
    {#snippet trigger(props)}
        <button
            {...props()}
            class={[
                "w-fit max-w-full truncate rounded-full px-2.5 py-0.5 font-bold",
                selected && "bg-peach",
                !selected &&
                    "text-cream-700 outline outline-cream-400 outline-dashed"
            ]}
        >
            {#if selected}
                <Tag class="inline text-sm" />
                {selected.name}
            {:else}
                {m["todos.category.trigger"]()}
            {/if}
        </button>
    {/snippet}

    <DeleteCategory
        onDelete={(id) => {
            if (selected?.id == id) {
                void onSelect(null);
            }
        }}
    />

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
            class="form-input h-13.75 w-full rounded-xl border-none bg-cream-500/10 ps-10 pe-12 text-lg font-medium outline-none placeholder:text-cream-600 focus:ring-0"
            placeholder={m["todos.category.placeholder"]()}
            maxlength={50}
        />
    </div>

    <div
        class={[
            "flex min-h-0 grow flex-col",
            !isError && !hasNoCategories && "mt-2"
        ]}
        aria-live="polite"
        aria-busy={categories.isFetching}
    >
        {#if isError}
            <ResourceError
                message={m["todos.category.error"]()}
                onRetry={() => categories.refetch()}
            />
        {:else if isLoading}
            <Categories isLoading />
        {:else}
            {#if showAddButton}
                <AddCategory
                    name={search}
                    onAdd={async (category) => {
                        queryClient.setQueryData(
                            categoriesQueryOptions.queryKey,
                            (categories) => [...(categories ?? []), category]
                        );
                        void onSelect(category);

                        search = "";
                    }}
                />
            {/if}

            {#if showAddButton && collection().size > 0}
                <hr class="mt-2 text-cream-300" />
            {/if}

            {#if hasNoCategories}
                <img
                    src={Jelly}
                    width={82}
                    height={85}
                    alt=""
                    decoding="async"
                    class="mx-auto mt-[10vh] w-full max-w-28"
                />
                <p
                    class="mx-auto mt-8 max-w-74 text-center text-lg font-medium"
                >
                    {m["todos.category.no-categories"]()}
                </p>
            {/if}

            <Categories
                list={collection().items}
                selectedId={selected?.id ?? null}
                onSelect={(category) => {
                    void onSelect(category);
                }}
            />
        {/if}
    </div>
</SheetDialog>
