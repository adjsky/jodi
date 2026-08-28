import { TOUCH_SLOP } from "$/shared/cfg/constants";

import { overlaysContent } from "./overlays-content";

import type { Attachment } from "svelte/attachments";

type Touch = {
    x: number;
    y: number;
    identifier: number;
    target: HTMLElement;
    editable: HTMLElement;
};

const INTERACTIVE_SELECTOR = [
    "a[href]",
    "button",
    "input",
    "label",
    "select",
    "textarea",
    '[role="button"]',
    "[contenteditable]"
].join(", ");

const EDITABLE_INPUT_TYPES = [
    "email",
    "number",
    "password",
    "search",
    "tel",
    "text",
    "url"
];

type RetainFocusOptions = {
    blurOnScroll?: boolean;
};

export const retainFocus =
    (options?: RetainFocusOptions): Attachment<HTMLElement> =>
    (element) => {
        if (!overlaysContent()) {
            return;
        }

        return options?.blurOnScroll
            ? retainFocusWithBlurOnScroll(element)
            : retainFocusWithoutBlurOnScroll(element);
    };

function retainFocusWithBlurOnScroll(element: HTMLElement) {
    let touch: Touch | null = null;

    function handleTouchStart(event: TouchEvent) {
        const editable = document.activeElement;
        const target = event.target;
        const startedTouch = event.changedTouches[0];

        if (
            !(editable instanceof HTMLElement) ||
            !(target instanceof HTMLElement) ||
            !isKeyboardEditable(editable) ||
            !startedTouch
        ) {
            touch = null;
            return;
        }

        touch = {
            x: startedTouch.clientX,
            y: startedTouch.clientY,
            identifier: startedTouch.identifier,
            target,
            editable
        };
    }

    function handleTouchMove(event: TouchEvent) {
        if (!touch) return;

        const movedTouch = findTouch(event.changedTouches, touch.identifier);
        if (!movedTouch) return;

        const distance = Math.hypot(
            movedTouch.clientX - touch.x,
            movedTouch.clientY - touch.y
        );
        if (distance < TOUCH_SLOP) return;

        if (!touch.editable.contains(touch.target)) {
            touch.editable.blur();
        }

        touch = null;
    }

    function handleTouchEnd(event: TouchEvent) {
        if (!touch || !findTouch(event.changedTouches, touch.identifier)) {
            return;
        }

        if (!touch.target.closest(INTERACTIVE_SELECTOR)) {
            event.preventDefault();
        }

        touch = null;
    }

    function handleTouchCancel() {
        touch = null;
    }

    element.addEventListener("touchstart", handleTouchStart);
    element.addEventListener("touchmove", handleTouchMove);
    element.addEventListener("touchend", handleTouchEnd, { passive: false });
    element.addEventListener("touchcancel", handleTouchCancel);

    return () => {
        element.removeEventListener("touchstart", handleTouchStart);
        element.removeEventListener("touchmove", handleTouchMove);
        element.removeEventListener("touchend", handleTouchEnd);
        element.removeEventListener("touchcancel", handleTouchCancel);
    };
}

function retainFocusWithoutBlurOnScroll(element: HTMLElement) {
    function handleTouchEnd(event: TouchEvent) {
        const editable = document.activeElement;

        if (
            editable instanceof HTMLElement &&
            isKeyboardEditable(editable) &&
            event.target instanceof Element &&
            !event.target.closest(INTERACTIVE_SELECTOR)
        ) {
            event.preventDefault();
        }
    }

    element.addEventListener("touchend", handleTouchEnd, { passive: false });

    return () => {
        element.removeEventListener("touchend", handleTouchEnd);
    };
}

function isKeyboardEditable(element: HTMLElement | null): boolean {
    if (element == null) {
        return false;
    }

    if (element instanceof HTMLInputElement) {
        return EDITABLE_INPUT_TYPES.includes(element.type);
    }

    if (element instanceof HTMLTextAreaElement) {
        return true;
    }

    return element.isContentEditable;
}

function findTouch(touches: TouchList, identifier: number) {
    for (const touch of touches) {
        if (touch.identifier == identifier) {
            return touch;
        }
    }
}
