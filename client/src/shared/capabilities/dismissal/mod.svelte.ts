import type { Getter } from "runed";

declare global {
    class CloseWatcher extends EventTarget {
        constructor(options?: CloseWatcherOptions);

        oncancel: ((this: CloseWatcher, event: Event) => void) | null;
        onclose: ((this: CloseWatcher, event: Event) => void) | null;

        request_close(): void;
        close(): void;
        destroy(): void;
    }

    interface CloseWatcherOptions {
        signal?: AbortSignal;
    }
}

type Registration = {
    request_close: VoidFunction;
};

const registrations: Registration[] = [];

function register(on_close: VoidFunction): VoidFunction {
    const watcher = typeof CloseWatcher !== "undefined" ? new CloseWatcher() : undefined;

    if (watcher) {
        watcher.onclose = on_close;
    }

    const registration: Registration = {
        request_close() {
            if (watcher) {
                watcher.request_close();
            } else {
                on_close();
            }
        }
    };

    registrations.push(registration);

    return () => {
        watcher?.destroy();

        const index = registrations.indexOf(registration);

        if (index !== -1) {
            registrations.splice(index, 1);
        }
    };
}

function use(is_open: Getter<boolean>, on_close: VoidFunction): void {
    $effect(() => {
        if (!is_open()) return;
        return register(on_close);
    });
}

function request_close(): boolean {
    const registration = registrations.at(-1);

    if (!registration) {
        return false;
    }

    registration.request_close();

    return true;
}

export const Dismissal = {
    register,
    use,
    request_close
};
