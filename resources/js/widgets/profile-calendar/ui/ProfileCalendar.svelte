<script lang="ts">
    import { page } from "@inertiajs/svelte";
    import { Calendar } from "@lucide/svelte";
    import { User } from "$/entities/user";
    import { WeekCarousel } from "$/features/filter-by-date";
    import { getLocale } from "$/paraglide/runtime";
    import dayjs from "dayjs";
    import { capitalize } from "remeda";

    // const calendar = new Calendar();

    // watch(
    //     () => calendar.pointer,
    //     () => {
    //         router.reload({
    //             data: {
    //                 d: calendar.pointer.format("YYYY-MM-DD")
    //             },
    //             replace: true
    //         });
    //     },
    //     { lazy: true }
    // );

    let day = $state(dayjs().locale(getLocale()));
</script>

<header class="flex h-12.5 items-center justify-between px-5">
    <button><Calendar class="text-2xl" /></button>
    <div class="absolute left-1/2 -translate-x-1/2">
        <h1 class="text-center text-lg font-bold">
            {capitalize(day.format("dddd"))}
        </h1>
        <h2 class="text-center text-xs text-cream-600">
            {new Intl.DateTimeFormat(day.locale(), {
                year: "numeric",
                month: "long"
            }).format(day.toDate())}
        </h2>
    </div>
    <User.Avatar name={$page.props.auth.user.name} />
</header>

<WeekCarousel bind:day />
