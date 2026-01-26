import { page, router } from "@inertiajs/svelte";
import lz from "lz-string";
import { fromStore, get } from "svelte/store";

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
    #name?: string | null;
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

    constructor(name?: string | null, options?: HistoryViewOptions) {
        this.#name = name;
        this.#options = options;
    }

    get meta() {
        return this.#meta;
    }

    get name() {
        return this.#hash.view?.slice(1);
    }

    isOpen(): boolean;
    isOpen(name: string): boolean;
    isOpen(name?: string): boolean {
        return (
            this.#hash.view == `#${typeof name == "string" ? name : this.#name}`
        );
    }

    push(meta?: T): Promise<void>;
    push(name: string, meta?: T): Promise<void>;
    push(nameOrMeta?: string | T, metaOrNothing?: T): Promise<void> {
        const name = typeof nameOrMeta == "string" ? nameOrMeta : this.#name;
        const meta = typeof nameOrMeta != "string" ? nameOrMeta : metaOrNothing;

        return router.push(this.#visitOptions(name, meta));
    }

    replace(meta?: T): Promise<void>;
    replace(name: string, meta?: T): Promise<void>;
    replace(nameOrMeta?: string | T, metaOrNothing?: T): Promise<void> {
        const name = typeof nameOrMeta == "string" ? nameOrMeta : this.#name;
        const meta = typeof nameOrMeta != "string" ? nameOrMeta : metaOrNothing;

        return router.replace(this.#visitOptions(name, meta));
    }

    updateMeta(meta: Partial<T>, options?: UpdateMetaOptions) {
        const visitOptions = this.#visitOptions(
            this.#hash.view.slice(1),
            { ...this.#meta, ...meta } as T,
            options?.viewTransition
        );
        if (options?.push) {
            return router.push(visitOptions);
        } else {
            return router.replace(visitOptions);
        }
    }

    back() {
        if (!get(page).__jodi_historyModals?.length) {
            return router.replace({
                preserveScroll: true,
                preserveState: true,
                url: this.#url.pathname + this.#url.search,
                viewTransition: this.#options?.viewTransition
            });
        } else {
            return router.back();
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
        name: string | null | undefined,
        meta: T | undefined,
        viewTransition?: boolean
    ): ClientSideVisitOptions {
        return {
            preserveScroll: true,
            preserveState: true,
            url: `${this.#url.pathname}${this.#url.search}#${typeof name == "string" ? name : this.#name}${meta ? `?${this.#compress(meta)}` : ""}`,
            __jodi_isHistoryModal: true,
            viewTransition: viewTransition ?? this.#options?.viewTransition
        };
    }
}
