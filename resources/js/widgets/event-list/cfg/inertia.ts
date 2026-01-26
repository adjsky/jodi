import { m } from "$/paraglide/messages";
import { optimistic as _optimistic } from "$/shared/inertia/visit/optimistic";

import { editView } from "../model/view";

import type { VisitOptions } from "@inertiajs/core";

export const visitOptions: VisitOptions = {
    only: ["events"],
    preserveState: true,
    preserveScroll: true,
    preserveUrl: true,
    replace: true
};

export const optimistic = {
    edit: (id: number, close = true) =>
        _optimistic(
            (prev, data) => ({
                events: prev.events.map((e: App.Data.EventDto) =>
                    e.id == id ? { ...e, ...data } : e
                )
            }),
            {
                error: m["events.errors.edit"](),
                onSuccess(props) {
                    const events = props.events as App.Data.EventDto[];

                    if (close) {
                        return editView.back();
                    }

                    const event = events.find((e) => e.id == id);
                    return editView.updateMeta({ event });
                }
            }
        ),
    delete: (id: number) =>
        _optimistic(
            (prev) => ({
                events: prev.events.filter((e: App.Data.EventDto) => e.id != id)
            }),
            {
                error: m["events.errors.delete"](),
                onSuccess: () => editView.back()
            }
        )
};
