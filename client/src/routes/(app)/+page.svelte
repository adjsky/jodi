<script>
    import { TodoService } from "#entities/todo/mod.ts";
    import { SelectedDate } from "#features/choose-date/mod.ts";
    import { m } from "#paraglide/messages.js";
    import CalendarCat from "#shared/assets/calendar-cat.svg";
    import { MobileCalendarPreview } from "#widgets/calendar/mod.ts";
    import { CreateEntryFab } from "#widgets/create-entry-fab/mod.ts";
    import { CurrentUser } from "#widgets/current-user/mod.ts";
    import { TodoList } from "#widgets/todo-list/mod.ts";

    const todos = TodoService.list(() => SelectedDate.current);
    const events = [];

    const has_no_events_and_todos = $derived(todos.current?.length === 0 && events?.length === 0);
</script>

<main>
    <MobileCalendarPreview>
        <CurrentUser />
    </MobileCalendarPreview>

    {#if has_no_events_and_todos}
        <img
            src={CalendarCat}
            width={300}
            height={231}
            alt=""
            decoding="async"
            class="mx-auto mt-[15vh] max-w-48"
        />
        <p class="mx-auto mt-8 max-w-74 text-center text-lg font-medium">
            {m["home.empty-day"]()}
        </p>
    {/if}

    {#if todos.current}
        <TodoList class="mt-4" hidden={has_no_events_and_todos} todos={todos.current} />
    {/if}

    <CreateEntryFab />
</main>
