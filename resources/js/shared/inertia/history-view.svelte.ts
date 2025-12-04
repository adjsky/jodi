import { page, router } from "@inertiajs/svelte";
import lz from "lz-string";
import { fromStore } from "svelte/store";

// TODO: somehow handle broken hashes?

export class HistoryView<T extends string | number | Record<string, unknown>> {
    #name?: string;

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

    constructor(name?: string) {
        this.#name = name;
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

    open(meta?: T): void;
    open(name: string, meta?: T): void;
    open(nameOrMeta?: string | T, metaOrNothing?: T): void {
        const name = typeof nameOrMeta == "string" ? nameOrMeta : this.#name;
        const meta = typeof nameOrMeta != "string" ? nameOrMeta : metaOrNothing;

        router.push({
            preserveScroll: true,
            preserveState: true,
            url: `${this.#url.pathname}${this.#url.search}#${typeof name == "string" ? name : this.#name}${meta ? `?${this.#compress(meta)}` : ""}`,
            __jodi_isHistoryModal: true
        });
    }

    close() {
        if (!fromStore(page).current.__jodi_isHistoryModal) {
            router.replace({
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
}
