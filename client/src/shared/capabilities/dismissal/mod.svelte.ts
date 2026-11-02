import type { Getter } from "runed";

declare global {
    class CloseWatcher extends EventTarget {
        constructor(options?: CloseWatcherOptions);

        oncancel: ((this: CloseWatcher, event: Event) => void) | null;
        onclose: ((this: CloseWatcher, event: Event) => void) | null;

        requestClose(): void;
        close(): void;
        destroy(): void;
    }

    interface CloseWatcherOptions {
        signal?: AbortSignal;
    }
}

type Registration = {
    requestClose: VoidFunction;
};

const registrations: Registration[] = [];

function register(onClose: VoidFunction): VoidFunction {
    const watcher =
        typeof CloseWatcher !== "undefined" ? new CloseWatcher() : undefined;

    if (watcher) {
        watcher.onclose = onClose;
    }

    const registration: Registration = {
        requestClose() {
            if (watcher) {
                watcher.requestClose();
            } else {
                onClose();
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

function use(isOpen: Getter<boolean>, onClose: VoidFunction): void {
    $effect(() => {
        if (!isOpen()) return;
        return register(onClose);
    });
}

function requestClose(): boolean {
    const registration = registrations.at(-1);

    if (!registration) {
        return false;
    }

    registration.requestClose();

    return true;
}

export const Dismissal = {
    register,
    use,
    requestClose
};
