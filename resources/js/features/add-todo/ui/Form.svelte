<script lang="ts">
    import { getLocale } from "$/paraglide/runtime";
    import { useSearchParams } from "$/shared/inertia/use-search-params.svelte";
    import dayjs from "dayjs";

    const searchParams = useSearchParams();
    const day = $state(dayjs(searchParams["d"]).locale(getLocale()));
</script>

<h4>
    {new Intl.DateTimeFormat(day.locale(), {
        day: "2-digit",
        year: "numeric",
        month: "long",
        weekday: "short"
    }).format(day.toDate())}
</h4>
