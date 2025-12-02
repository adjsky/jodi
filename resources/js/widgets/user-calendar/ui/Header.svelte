<script lang="ts">
    import { Link, page } from "@inertiajs/svelte";
    import { Calendar } from "@lucide/svelte";
    import { User } from "$/entities/user";
    import { WeekCarousel } from "$/features/filter-by-date";
    import { getLocale } from "$/paraglide/runtime";
    import { useSearchParams } from "$/shared/inertia/use-search-params.svelte";
    import dayjs from "dayjs";
    import { capitalize } from "remeda";

    const searchParams = useSearchParams({ showProgress: true });
    const day = $derived(dayjs(searchParams["d"]).locale(getLocale()));
</script>

<header class="flex items-center justify-between pr-6 pl-3">
    <button class="p-2.5"><Calendar class="text-3xl" /></button>
    <div class="absolute left-1/2 -translate-x-1/2">
        <h1 class="text-center text-xl font-bold">
            {capitalize(day.format("dddd"))}
        </h1>
        <h2 class="text-center text-sm text-cream-600">
            {new Intl.DateTimeFormat(day.locale(), {
                year: "numeric",
                month: "long"
            }).format(day.toDate())}
        </h2>
    </div>
    <Link href="/me"><User.Avatar name={$page.props.auth.user.name} /></Link>
</header>

<WeekCarousel
    bind:day={() => day, (v) => (searchParams.d = v.format("YYYY-MM-DD"))}
/>
