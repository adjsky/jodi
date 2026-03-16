import { router } from "@inertiajs/core";
import { m } from "$/paraglide/messages";
import { WEEK_CAROUSEL_CACHE_TAG } from "$/shared/cfg/constants";
import { optimistic as _optimistic } from "$/shared/inertia/visit/optimistic";
import { normalizeIsoString } from "$/shared/lib/date";
import * as PushSubscription from "$/shared/lib/push-subscription.svelte";

import { editView } from "../model/view";

import type { VisitOptions } from "@inertiajs/core";

export const visitOptions: VisitOptions = {
    only: ["todos", "categories"],
    preserveState: true,
    preserveScroll: true,
    preserveUrl: true,
    replace: true
};

export const optimistic = {
    edit: (id: number, withAhtungReminder: boolean) =>
        _optimistic(
            (prev, data) => ({
                todos: prev.todos.map((t: App.Data.TodoDto) =>
                    t.id == id ? { ...t, ...data } : t
                )
            }),
            {
                error: m["todos.errors.edit"](),
                onSuccess: () => {
                    if (withAhtungReminder) {
                        PushSubscription.ahtung(m["todos.reminder-ahtung"]());
                    }

                    router.flushByCacheTags(WEEK_CAROUSEL_CACHE_TAG);

                    void editView.back();
                }
            }
        ),
    complete: (id: number) =>
        _optimistic(
            (prev) => ({
                todos: prev.todos.map((t: App.Data.TodoDto) =>
                    t.id == id
                        ? {
                              ...t,
                              completedAt: t.completedAt
                                  ? null
                                  : normalizeIsoString(new Date().toISOString())
                          }
                        : t
                )
            }),
            {
                error: m["todos.errors.complete"](),
                onSuccess(props) {
                    const todos = props.todos as App.Data.TodoDto[];

                    const todo = todos.find((t) => t.id == id);
                    if (!todo || !editView.isOpen()) return;

                    return editView.updateMeta(todo);
                }
            }
        ),
    delete: (id: number) =>
        _optimistic(
            (prev) => ({
                todos: prev.todos.filter((t: App.Data.TodoDto) => t.id != id)
            }),
            {
                error: m["todos.errors.delete"](),
                onSuccess: () => {
                    router.flushByCacheTags(WEEK_CAROUSEL_CACHE_TAG);
                    void editView.back();
                }
            }
        )
};
