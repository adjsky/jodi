import { noop } from "#shared/lib/function/noop.ts";
import { goto } from "$app/navigation";
import { page } from "$app/state";

export type OverlayName =
    | "login/get-started"
    | "login/email"
    | "login/verify"
    | "create-entry"
    | "create-todo"
    | "create-event"
    | "edit-todo"
    | "edit-event"
    | "calendar"
    | "me"
    | "me/name"
    | "me/email"
    | "me/friends"
    | "me/invitations"
    | "me/invitations/add"
    | "me/language"
    | "me/week-start"
    | "me/notifications";

export type OverlayMeta = {
    loginchallenge?: { email: string; id: string };
    event?: { id: string };
    todo?: { id: string };
    invitation?: { id: string };
    calendarpicker?: { isOpen: boolean };
    timepicker?: { isOpen: boolean };
    selectcategory?: { isOpen: boolean };
    selectrecurrence?: { isOpen: boolean };
    selectreminder?: { isOpen: boolean };
    deleterecurrable?: { isOpen: boolean };
    saverecurrable?: { isOpen: boolean };
    deleteinvitation?: { isOpen: boolean };
};

export type OverlayState = {
    name: OverlayName;
    meta: OverlayMeta | null;
};

function isOpen(
    name: OverlayName,
    predicate?: (meta: OverlayMeta | null) => boolean
): boolean {
    const overlay = page.state.overlay;

    return overlay?.name === name && (!predicate || predicate(overlay.meta));
}

async function open(
    name: OverlayName,
    meta: OverlayMeta | null = null,
    options?: { replace?: boolean; skipViewTransition?: boolean }
): Promise<void> {
    return goto("", {
        shallow: true,
        persistState: true,
        state: {
            ...page.state,
            overlay: { name, meta },
            skipViewTransition: options?.skipViewTransition ?? false
        },
        replace: options?.replace
    });
}

async function close(levels = 1): Promise<void> {
    if (levels >= history.length) {
        return;
    }

    const previousState = page.state;
    let dispose = noop;
    let timeout: number | undefined;

    try {
        await new Promise<void>((resolve) => {
            let observed = false;
            timeout = window.setTimeout(resolve, 5_000);
            dispose = $effect.root(() => {
                $effect(() => {
                    if (observed || page.state === previousState) return;
                    observed = true;
                    resolve();
                });
            });
            history.go(-levels);
        });
    } finally {
        window.clearTimeout(timeout);
        dispose();
    }
}

export const Overlay = {
    isOpen,
    open,
    close,
    get name(): OverlayName | null {
        return page.state.overlay?.name ?? null;
    },
    get meta(): OverlayMeta | null {
        return page.state.overlay?.meta ?? null;
    }
};
