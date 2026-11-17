import { parseDate, parseZonedDateTime } from "@internationalized/date";
import { RecurrenceExceptionRepository } from "#entities/recurrence/model/recurrence-exception-repository.ts";
import { beforeEach, expect, it, vi } from "vitest";

import { TodoPositionRepository } from "./todo-position-repository.ts";
import { TodoRepository } from "./todo-repository.ts";
import { TodoService } from "./todo-service.ts";

import type { Todo, TodoDraft } from "./types.ts";

vi.mock("./todo-repository.ts");
vi.mock("./todo-position-repository.ts");
vi.mock("#entities/recurrence/model/recurrence-exception-repository.ts");
vi.mock("#shared/db/live-query.svelte.ts");
vi.mock("#shared/cfg/constants.ts", () => ({ TIMEZONE: "UTC", UTC: "UTC" }));
vi.mock("#shared/db/dexie.ts", () => ({
    db: {
        transaction: vi.fn(async (mode: string, tables: unknown[], run: () => Promise<unknown>) =>
            run()
        )
    }
}));

beforeEach(() => {
    vi.resetAllMocks();
});

function given_todo(attributes: Partial<Todo> = {}): Todo {
    const todo: Todo = {
        id: "todo-1",
        title: "Original",
        description: "",
        category_id: "category-1",
        color: null,
        rrule: "FREQ=DAILY",
        scheduled_at: parseZonedDateTime("2030-01-01T18:00[UTC]"),
        has_time: true,
        completed_at: null,
        created_at: parseZonedDateTime("2029-12-01T12:00[UTC]"),
        notify_before: "",
        timezone: "UTC",
        ...attributes
    };
    vi.mocked(TodoRepository.find).mockResolvedValue(todo);

    return todo;
}

function draft_for(todo: Todo, attributes: Partial<TodoDraft> = {}): TodoDraft {
    return {
        title: todo.title,
        description: todo.description,
        category_id: todo.category_id,
        color: todo.color,
        rrule: todo.rrule,
        scheduled_at: parseZonedDateTime("2030-01-08T18:00[UTC]"),
        has_time: todo.has_time,
        notify_before: todo.notify_before,
        ...attributes
    };
}

it("updates a nonrecurring todo through the regular update path", async () => {
    const todo = given_todo({ rrule: null });
    const draft = draft_for(todo, {
        title: "Updated",
        has_time: false,
        scheduled_at: todo.scheduled_at
    });

    await TodoService.update(todo.id, draft, null, "this");

    expect(TodoRepository.update).toHaveBeenCalledWith(todo.id, draft);
    expect(RecurrenceExceptionRepository.upsert).not.toHaveBeenCalled();
    expect(TodoPositionRepository.destroy_all).not.toHaveBeenCalled();
});

it("updates an occurrence without changing the series or losing completion and null overrides", async () => {
    const todo = given_todo();
    const completed_at = parseZonedDateTime("2030-01-08T12:00[UTC]");
    vi.mocked(RecurrenceExceptionRepository.find).mockResolvedValue({
        id: "exception-1",
        recurrenceable_type: "todo",
        recurrenceable_id: todo.id,
        occurs_at: "2030-01-08",
        is_cancelled: false,
        overrides: { title: "Edited", category_id: null, completed_at }
    });

    await TodoService.update(
        todo.id,
        draft_for(todo, {
            title: "Updated",
            category_id: null,
            has_time: false
        }),
        parseDate("2030-01-08"),
        "this"
    );

    expect(RecurrenceExceptionRepository.upsert).toHaveBeenCalledWith({
        id: "exception-1",
        recurrenceable_type: "todo",
        recurrenceable_id: todo.id,
        occurs_at: "2030-01-08",
        is_cancelled: false,
        overrides: {
            title: "Updated",
            category_id: null,
            completed_at,
            has_time: false
        }
    });
    expect(TodoRepository.update).not.toHaveBeenCalled();
    expect(TodoPositionRepository.destroy_for_date).not.toHaveBeenCalled();
});

it("compares occurrence timestamps in UTC across a local calendar date boundary", async () => {
    const todo = given_todo({
        scheduled_at: parseZonedDateTime("2030-01-02T03:00+09:00[Asia/Tokyo]")
    });
    vi.mocked(RecurrenceExceptionRepository.find).mockResolvedValue(null);

    await TodoService.update(
        todo.id,
        draft_for(todo, {
            title: "Updated",
            scheduled_at: parseZonedDateTime("2030-01-09T03:00+09:00[Asia/Tokyo]")
        }),
        parseDate("2030-01-08"),
        "this"
    );

    expect(RecurrenceExceptionRepository.upsert).toHaveBeenCalledWith(
        expect.objectContaining({ overrides: { title: "Updated" } })
    );
    expect(TodoPositionRepository.destroy_for_date).not.toHaveBeenCalled();
});

it("clears the displayed position when moving an already rescheduled occurrence", async () => {
    const todo = given_todo();
    const scheduled_at = parseZonedDateTime("2030-01-10T18:00[UTC]");
    vi.mocked(RecurrenceExceptionRepository.find).mockResolvedValue({
        id: "exception-1",
        recurrenceable_type: "todo",
        recurrenceable_id: todo.id,
        occurs_at: "2030-01-08",
        is_cancelled: false,
        overrides: { scheduled_at: parseZonedDateTime("2030-01-09T18:00[UTC]") }
    });

    await TodoService.update(
        todo.id,
        draft_for(todo, { scheduled_at }),
        parseDate("2030-01-08"),
        "this"
    );

    expect(TodoPositionRepository.destroy_for_date).toHaveBeenCalledWith(
        todo.id,
        parseDate("2030-01-09")
    );
    expect(RecurrenceExceptionRepository.upsert).toHaveBeenCalledWith(
        expect.objectContaining({ overrides: { scheduled_at } })
    );
});

it("rejects a single-occurrence update without its occurrence date before writing", async () => {
    const todo = given_todo();

    await expect(TodoService.update(todo.id, draft_for(todo), null, "this")).rejects.toThrow(
        "No occurs_at."
    );

    expect(TodoRepository.update).not.toHaveBeenCalled();
    expect(RecurrenceExceptionRepository.upsert).not.toHaveBeenCalled();
    expect(TodoPositionRepository.destroy_for_date).not.toHaveBeenCalled();
});

it("normalizes a global edit to the series start without splitting equivalent rules", async () => {
    const todo = given_todo({ rrule: "FREQ=WEEKLY;BYDAY=TU" });
    const draft = draft_for(todo, {
        rrule: "BYDAY=TU;FREQ=WEEKLY",
        scheduled_at: parseZonedDateTime("2030-01-08T19:00[UTC]")
    });

    await TodoService.update(todo.id, draft, parseDate("2030-01-08"), "all");

    expect(TodoRepository.update).toHaveBeenCalledWith(todo.id, {
        ...draft,
        scheduled_at: parseZonedDateTime("2030-01-01T19:00[UTC]")
    });
    expect(RecurrenceExceptionRepository.reset).toHaveBeenCalledWith("todo", todo.id, [
        "title",
        "description",
        "category_id",
        "color",
        "scheduled_at",
        "has_time",
        "notify_before"
    ]);
    expect(TodoRepository.create).not.toHaveBeenCalled();
    expect(draft.scheduled_at).toEqual(parseZonedDateTime("2030-01-08T19:00[UTC]"));
});

it("splits a changed rule and transfers future exceptions and ordering to the new todo", async () => {
    const todo = given_todo({ rrule: "FREQ=DAILY;COUNT=20" });
    const draft = draft_for(todo, { rrule: "FREQ=WEEKLY" });
    const date = parseDate("2030-01-08");
    vi.mocked(TodoRepository.create).mockResolvedValue("todo-2");

    await TodoService.update(todo.id, draft, date, "all");

    expect(TodoRepository.create).toHaveBeenCalledWith({ ...todo, ...draft });
    expect(TodoRepository.update).toHaveBeenCalledWith(todo.id, {
        ...draft,
        scheduled_at: todo.scheduled_at,
        rrule: "RRULE:FREQ=DAILY;UNTIL=20300107T235959Z"
    });
    expect(RecurrenceExceptionRepository.transfer).toHaveBeenCalledWith(
        "todo",
        todo.id,
        "todo-2",
        date
    );
    expect(TodoPositionRepository.transfer).toHaveBeenCalledWith(todo.id, "todo-2", date);
    expect(TodoPositionRepository.destroy_all).not.toHaveBeenCalled();
});

it("deletes future exceptions when a split disables recurrence", async () => {
    const todo = given_todo();
    const date = parseDate("2030-01-08");
    vi.mocked(TodoRepository.create).mockResolvedValue("todo-2");

    await TodoService.update(todo.id, draft_for(todo, { rrule: null }), date, "all");

    expect(RecurrenceExceptionRepository.destroy).toHaveBeenCalledWith("todo", todo.id, date);
    expect(RecurrenceExceptionRepository.transfer).not.toHaveBeenCalled();
});

it("preserves transferred positions when the split todo already has the requested category", async () => {
    const todo = given_todo();
    vi.mocked(TodoRepository.create).mockResolvedValue("todo-2");

    await TodoService.update(
        todo.id,
        draft_for(todo, { rrule: "FREQ=WEEKLY", category_id: null }),
        parseDate("2030-01-08"),
        "all"
    );

    expect(vi.mocked(TodoPositionRepository.destroy_all).mock.calls).toEqual([[todo.id]]);
});

it("clears positions for both todos when a split moves to another occurrence date", async () => {
    const todo = given_todo();
    const scheduled_at = parseZonedDateTime("2030-01-09T18:00[UTC]");
    vi.mocked(TodoRepository.create).mockResolvedValue("todo-2");

    await TodoService.update(
        todo.id,
        draft_for(todo, { rrule: "FREQ=WEEKLY", scheduled_at }),
        parseDate("2030-01-08"),
        "all"
    );

    expect(vi.mocked(TodoPositionRepository.destroy_all).mock.calls).toEqual([
        ["todo-2"],
        [todo.id]
    ]);
    expect(TodoRepository.update).toHaveBeenCalledWith(
        todo.id,
        expect.objectContaining({
            scheduled_at,
            rrule: "RRULE:FREQ=DAILY;UNTIL=20300108T235959Z"
        })
    );
});
