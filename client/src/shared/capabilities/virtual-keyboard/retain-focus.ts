import { TOUCH_SLOP } from "#shared/cfg/constants.ts";

import { OVERLAYS_CONTENT } from "./overlays-content.ts";

import type { Attachment } from "svelte/attachments";

type RetainFocusTouch = {
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

const EDITABLE_INPUT_TYPES = ["email", "number", "password", "search", "tel", "text", "url"];

type RetainFocusOptions = {
    blur_on_scroll?: boolean;
};

export const retain_focus =
    (options?: RetainFocusOptions): Attachment<HTMLElement> =>
    (element) => {
        if (!OVERLAYS_CONTENT) {
            return;
        }

        return options?.blur_on_scroll
            ? retain_focus_with_blur_on_scroll(element)
            : retain_focus_without_blur_on_scroll(element);
    };

function retain_focus_with_blur_on_scroll(element: HTMLElement) {
    let touch: RetainFocusTouch | null = null;

    function handle_touch_start(event: TouchEvent) {
        const editable = document.activeElement;
        const target = event.target;
        const started_touch = event.changedTouches[0];

        if (
            !(editable instanceof HTMLElement) ||
            !(target instanceof HTMLElement) ||
            !is_keyboard_editable(editable) ||
            !started_touch
        ) {
            touch = null;
            return;
        }

        touch = {
            x: started_touch.clientX,
            y: started_touch.clientY,
            identifier: started_touch.identifier,
            target,
            editable
        };
    }

    function handle_touch_move(event: TouchEvent) {
        if (!touch) return;

        const moved_touch = find_touch(event.changedTouches, touch.identifier);
        if (!moved_touch) return;

        const distance = Math.hypot(moved_touch.clientX - touch.x, moved_touch.clientY - touch.y);
        if (distance < TOUCH_SLOP) return;

        if (!touch.editable.contains(touch.target)) {
            touch.editable.blur();
        }

        touch = null;
    }

    function handle_touch_end(event: TouchEvent) {
        if (!touch || !find_touch(event.changedTouches, touch.identifier)) {
            return;
        }

        if (!touch.target.closest(INTERACTIVE_SELECTOR)) {
            event.preventDefault();
        }

        touch = null;
    }

    function handle_touch_cancel() {
        touch = null;
    }

    element.addEventListener("touchstart", handle_touch_start);
    element.addEventListener("touchmove", handle_touch_move);
    element.addEventListener("touchend", handle_touch_end, { passive: false });
    element.addEventListener("touchcancel", handle_touch_cancel);

    return () => {
        element.removeEventListener("touchstart", handle_touch_start);
        element.removeEventListener("touchmove", handle_touch_move);
        element.removeEventListener("touchend", handle_touch_end);
        element.removeEventListener("touchcancel", handle_touch_cancel);
    };
}

function retain_focus_without_blur_on_scroll(element: HTMLElement) {
    function handle_touch_end(event: TouchEvent) {
        const editable = document.activeElement;

        if (
            editable instanceof HTMLElement &&
            is_keyboard_editable(editable) &&
            event.target instanceof Element &&
            !event.target.closest(INTERACTIVE_SELECTOR)
        ) {
            event.preventDefault();
        }
    }

    element.addEventListener("touchend", handle_touch_end, { passive: false });

    return () => {
        element.removeEventListener("touchend", handle_touch_end);
    };
}

function is_keyboard_editable(element: HTMLElement | null): boolean {
    if (element === null) {
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

function find_touch(touches: TouchList, identifier: number) {
    for (const touch of touches) {
        if (touch.identifier === identifier) {
            return touch;
        }
    }
}
