import { page, router } from "@inertiajs/svelte";
import lz from "lz-string";
import { fromStore } from "svelte/store";

import type { ClientSideVisitOptions } from "@inertiajs/core";

// TODO: somehow handle broken hashes?

export type HistoryViewOptions = {
    viewTransition?: boolean;
};

export type UpdateMetaOptions = {
    push?: boolean;
    viewTransition?: boolean;
};

export class HistoryView<T extends Record<string, unknown>> {
    #name?: string;
    #options?: HistoryViewOptions;

    #url = $derived(new URL(fromStore(page).current.url, location.origin));

    #hash = $derived.by(() => {
        const [view, meta] = this.#url.hash.split("?");
        return { view, meta };
    });
    #meta = $derived.by(() => {
        if (!this.#hash.meta) {
            return null;
        }
        return this.#decompress(this.#hash.meta) as T;
    });

    constructor(name?: string, options?: HistoryViewOptions) {
        this.#name = name;
        this.#options = options;
    }

    get meta() {
        return this.#meta;
    }

    isOpen(): boolean;
    isOpen(name: string): boolean;
    isOpen(name?: string): boolean {
        return (
            this.#hash.view == `#${typeof name == "string" ? name : this.#name}`
        );
    }

    open(meta?: T): Promise<void>;
    open(name: string, meta?: T): Promise<void>;
    open(nameOrMeta?: string | T, metaOrNothing?: T): Promise<void> {
        const name = typeof nameOrMeta == "string" ? nameOrMeta : this.#name;
        const meta = typeof nameOrMeta != "string" ? nameOrMeta : metaOrNothing;

        return router.push(this.#visitOptions(name, meta));
    }

    updateMeta(meta: T, options?: UpdateMetaOptions) {
        const visitOptions = this.#visitOptions(
            this.#hash.view.slice(1),
            meta,
            options?.viewTransition
        );
        if (options?.push) {
            void router.push(visitOptions);
        } else {
            void router.replace(visitOptions);
        }
    }

    back() {
        if (!fromStore(page).current.__jodi_isHistoryModal) {
            void router.replace({
                preserveScroll: true,
                preserveState: true,
                url: this.#url.pathname + this.#url.search
            });
        } else {
            history.back();
        }
    }

    #compress(data: T) {
        return lz.compressToEncodedURIComponent(JSON.stringify(data)!);
    }

    #decompress(encoded: string) {
        try {
            return JSON.parse(lz.decompressFromEncodedURIComponent(encoded));
        } catch (e) {
            console.warn(e);
            return null;
        }
    }

    #visitOptions(
        name: string | undefined,
        meta: T | undefined,
        ViewTransition?: boolean
    ): ClientSideVisitOptions {
        return {
            preserveScroll: true,
            preserveState: true,
            url: `${this.#url.pathname}${this.#url.search}#${typeof name == "string" ? name : this.#name}${meta ? `?${this.#compress(meta)}` : ""}`,
            __jodi_isHistoryModal: true,
            viewTransition: ViewTransition ?? this.#options?.viewTransition
        };
    }
}
