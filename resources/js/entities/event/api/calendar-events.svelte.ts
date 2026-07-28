import ListCalendarEvents from "$/generated/actions/App/Domain/Event/Actions/ListCalendarEvents";
import { useDebounce } from "runed";

import type { DateValue } from "@internationalized/date";
import type { CalendarEventData } from "$/entities/event/model/types";

type Options = {
    onError?: VoidFunction;
    onSuccess?: (
        year: number,
        months: number[],
        events: CalendarEventData[]
    ) => void;
};

type PendingRequests = {
    version: number;
    year: number;
    months: Set<number>;
};

export class CalendarEvents {
    #options: Options;
    #abortController: AbortController | null = null;
    #pendingRequests: PendingRequests | null = null;
    #requestCounter = 0;

    constructor(options: Options) {
        this.#options = options;
    }

    request(date: DateValue) {
        const version = ++this.#requestCounter;

        if (this.#pendingRequests && this.#pendingRequests.year == date.year) {
            this.#pendingRequests.months.add(date.month);
            this.#pendingRequests.version = version;
        } else {
            this.#pendingRequests = {
                version,
                year: date.year,
                months: new Set([date.month])
            };
        }

        void this.#request(structuredClone(this.#pendingRequests));
    }

    #request = useDebounce(async (requests: PendingRequests) => {
        if (this.#pendingRequests?.version == requests.version) {
            this.#pendingRequests = null;
        }

        this.#abortController?.abort();
        this.#abortController = new AbortController();

        try {
            const { url, method } = ListCalendarEvents(
                { year: requests.year },
                { query: { m: [...requests.months].join(",") } }
            );

            const response = await fetch(url, {
                method,
                signal: this.#abortController.signal
            });
            const events = (await response.json()) as CalendarEventData[];

            this.#options.onSuccess?.(
                requests.year,
                Array.from(requests.months),
                events
            );
        } catch (e) {
            if (e instanceof DOMException && e.name === "AbortError") return;
            console.error(e);
            this.#options.onError?.();
        }
    }, 50);
}
