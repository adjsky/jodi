import ListCalendarEvents from "$/generated/actions/App/Domain/Event/Actions/ListCalendarEvents";
import ky from "ky";
import { useDebounce } from "runed";

import type { CalendarEventData } from "../model/types";
import type { DateValue } from "@internationalized/date";

type Options = {
    on_error?: VoidFunction;
    on_success?: (year: number, months: number[], events: CalendarEventData[]) => void;
};

type PendingRequests = {
    version: number;
    year: number;
    months: Set<number>;
};

export class CalendarEvents {
    #options: Options;
    #abort_controller: AbortController | null = null;
    #pending_requests: PendingRequests | null = null;
    #request_counter = 0;

    constructor(options: Options) {
        this.#options = options;
    }

    request(date: DateValue): void {
        const version = ++this.#request_counter;

        if (this.#pending_requests && this.#pending_requests.year === date.year) {
            this.#pending_requests.months.add(date.month);
            this.#pending_requests.version = version;
        } else {
            this.#pending_requests = {
                version,
                year: date.year,
                months: new Set([date.month])
            };
        }

        void this.#request(structuredClone(this.#pending_requests));
    }

    #request = useDebounce(async (requests: PendingRequests) => {
        if (this.#pending_requests?.version === requests.version) {
            this.#pending_requests = null;
        }

        this.#abort_controller?.abort();
        this.#abort_controller = new AbortController();

        try {
            const { url, method } = ListCalendarEvents(
                { year: requests.year },
                { query: { m: [...requests.months].join(",") } }
            );

            const events: CalendarEventData[] = await ky(url, {
                method,
                signal: this.#abort_controller.signal
            }).json();

            this.#options.on_success?.(requests.year, Array.from(requests.months), events);
        } catch (e) {
            if (e instanceof DOMException && e.name === "AbortError") return;
            console.error(e);
            this.#options.on_error?.();
        }
    }, 50);
}
