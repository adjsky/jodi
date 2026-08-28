import { page, router } from "@inertiajs/svelte";
import lz from "lz-string";

import type { ClientSideVisitOptions } from "@inertiajs/core";

// TODO: somehow handle broken hashes?

type HistoryViewOptions = {
    viewTransition?: boolean;
};

type UpdateMetaOptions = {
    push?: boolean;
    viewTransition?: boolean;
};

type PushReplaceOptions<T> = {
    meta?: T;
    search?: Record<string, string>;
    viewTransition?: boolean;
};

export class HistoryView<T extends Record<string, unknown>> {
    #name?: string | null;
    #options?: HistoryViewOptions;

    constructor(name?: string | null, options?: HistoryViewOptions) {
        this.#name = name;
        this.#options = options;
    }

    get #url(): URL {
        return new URL(page.url, location.origin);
    }

    get #hash() {
        const [view, meta] = this.#url.hash.split("?");

        return { view, meta };
    }

    get meta(): T | null {
        const meta = this.#hash.meta;

        if (!meta) {
            return null;
        }

        return this.#decompress(meta) as T;
    }

    get name(): string {
        return this.#hash.view.slice(1);
    }

    isOpen(): boolean;
    isOpen(name: string): boolean;
    isOpen(name?: string): boolean {
        return (
            this.#hash.view == `#${typeof name == "string" ? name : this.#name}`
        );
    }

    push(options?: PushReplaceOptions<T>): Promise<void>;
    push(name: string, options?: PushReplaceOptions<T>): Promise<void>;
    push(
        nameOrOptions?: string | PushReplaceOptions<T>,
        optionsOrNothing?: PushReplaceOptions<T>
    ): Promise<void> {
        const name =
            typeof nameOrOptions == "string" ? nameOrOptions : this.#name;
        const options =
            typeof nameOrOptions != "string" ? nameOrOptions : optionsOrNothing;

        return router.push(
            this.#visitOptions(
                name,
                options?.meta,
                options?.viewTransition,
                options?.search
            )
        );
    }

    replace(options?: PushReplaceOptions<T>): Promise<void>;
    replace(name: string, options?: PushReplaceOptions<T>): Promise<void>;
    replace(
        nameOrOptions?: string | PushReplaceOptions<T>,
        optionsOrNothing?: PushReplaceOptions<T>
    ): Promise<void> {
        const name =
            typeof nameOrOptions == "string" ? nameOrOptions : this.#name;
        const options =
            typeof nameOrOptions != "string" ? nameOrOptions : optionsOrNothing;

        return router.replace(
            this.#visitOptions(
                name,
                options?.meta,
                options?.viewTransition,
                options?.search
            )
        );
    }

    updateMeta(meta: Partial<T>, options?: UpdateMetaOptions): Promise<void> {
        const visitOptions = this.#visitOptions(
            this.#hash.view.slice(1),
            { ...this.meta, ...meta } as T,
            options?.viewTransition ?? this.#options?.viewTransition
        );
        if (options?.push) {
            return router.push(visitOptions);
        } else {
            return router.replace(visitOptions);
        }
    }

    back(): Promise<void> {
        if (!page.__jodi_historyModals?.length) {
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
        viewTransition?: boolean,
        search?: Record<string, string>
    ): ClientSideVisitOptions {
        const hash = `${typeof name == "string" ? name : this.#name}${meta ? `?${this.#compress(meta)}` : ""}`;

        return {
            preserveScroll: true,
            preserveState: true,
            url: `${this.#url.pathname}${this.#buildSearchString(search)}#${hash}`,
            __jodi_isHistoryModal: true,
            viewTransition: viewTransition ?? this.#options?.viewTransition
        };
    }

    #buildSearchString(search?: Record<string, string>) {
        if (!search) {
            return this.#url.search;
        }

        const sp = new URLSearchParams(search).toString();

        return sp ? `?${sp}` : "";
    }
}
