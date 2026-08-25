import { goto } from "$app/navigation";
import { page } from "$app/state";

export type OverlayName =
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
    | `me/invitation/${string}`
    | "me/language"
    | "me/week-start"
    | "me/notifications";

export type OverlayMeta = {
    event?: { id: string };
    todo?: { id: string };
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
    meta: OverlayMeta | null,
    options?: { replace?: boolean }
): Promise<void> {
    return goto("", {
        shallow: true,
        persistState: true,
        state: { ...page.state, overlay: { name, meta } },
        replace: options?.replace
    });
}

function close(levels = 1): void {
    history.go(-levels);
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
