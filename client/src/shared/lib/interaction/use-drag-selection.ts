import { TOUCH_SLOP } from "#shared/cfg/constants.ts";
import { extract } from "runed";

import type { MaybeGetter } from "runed";
import type { Attachment } from "svelte/attachments";

const SELECTION_DELAY = 350;

type Options<T> = {
    enabled: boolean;
    is_selectable?: (value: T) => boolean;
    on_start?: VoidFunction;
    on_select?: (value: T, anchor: T, phase: "start" | "move") => void;
    on_end?: VoidFunction;
};

export type DragSelection<T> = {
    attachment: (value: T) => Attachment<HTMLElement>;
};

export function use_drag_selection<T>(options: MaybeGetter<Options<T>>): DragSelection<T> {
    const values = new WeakMap<Element, T>();

    let timer: NodeJS.Timeout | null = null;
    let anchor_element: HTMLElement | null = null;
    let anchor_value: T | null = null;
    let current_value: T | null = null;
    let pointer_id: number | null = null;
    let is_selecting = false;
    let suppress_click = false;
    let start_position = [0, 0];

    return {
        attachment: (value) => (element) => {
            values.set(element, value);

            function onpointerdown(e: PointerEvent) {
                const { enabled, is_selectable, on_start, on_select } = extract(options);

                if (
                    e.pointerType !== "touch" ||
                    !enabled ||
                    is_selectable?.(value) === false ||
                    !e.isPrimary ||
                    pointer_id !== null
                ) {
                    return;
                }

                window.addEventListener("pointerup", onpointerend);
                window.addEventListener("pointercancel", onpointerend);
                window.addEventListener("touchmove", ontouchmove, {
                    passive: false
                });

                anchor_element = element;
                anchor_value = value;
                current_value = value;
                pointer_id = e.pointerId;
                start_position = [e.clientX, e.clientY];

                timer = setTimeout(() => {
                    if (anchor_value === null) return;

                    timer = null;
                    is_selecting = true;
                    suppress_click = true;

                    on_start?.();
                    on_select?.(value, anchor_value, "start");
                }, SELECTION_DELAY);
            }

            function onpointermove(e: PointerEvent) {
                const { is_selectable, on_select } = extract(options);

                if (e.pointerId !== pointer_id) return;

                if (!is_selecting) {
                    const [x, y] = start_position;
                    const distance = Math.hypot(e.clientX - x, e.clientY - y);
                    if (distance >= TOUCH_SLOP) reset();
                    return;
                }

                if (anchor_value === null) return;

                let element = document.elementFromPoint(e.clientX, e.clientY);

                while (element) {
                    const value = values.get(element);
                    if (value !== undefined) {
                        if (value !== current_value && is_selectable?.(value) !== false) {
                            current_value = value;
                            on_select?.(value, anchor_value, "move");
                        }
                        break;
                    }
                    element = element.parentElement;
                }
            }

            function onpointerend(e: PointerEvent) {
                const { on_end } = extract(options);

                if (e.pointerId !== pointer_id) return;

                const was_selecting = is_selecting;
                const was_cancelled = e.type === "pointercancel";

                reset();

                if (was_selecting) {
                    on_end?.();
                }

                if (was_cancelled) {
                    suppress_click = false;
                } else if (was_selecting) {
                    setTimeout(() => (suppress_click = false), 0);
                }
            }

            function onclick(e: PointerEvent) {
                if (!suppress_click) return;

                suppress_click = false;

                e.preventDefault();
                e.stopImmediatePropagation();
            }

            function ontouchmove(e: TouchEvent) {
                if (is_selecting && e.cancelable) {
                    e.preventDefault();
                }
            }

            function reset() {
                window.removeEventListener("pointerup", onpointerend);
                window.removeEventListener("pointercancel", onpointerend);
                window.removeEventListener("touchmove", ontouchmove);

                if (timer !== null) {
                    clearTimeout(timer);
                    timer = null;
                }

                anchor_element = null;
                anchor_value = null;
                current_value = null;
                pointer_id = null;
                is_selecting = false;
                start_position = [0, 0];
            }

            element.addEventListener("click", onclick, { capture: true });
            element.addEventListener("pointerdown", onpointerdown);
            element.addEventListener("pointermove", onpointermove);

            return () => {
                if (anchor_element === element) {
                    if (is_selecting) extract(options).on_end?.();
                    suppress_click = false;
                    reset();
                }

                values.delete(element);

                element.removeEventListener("click", onclick, {
                    capture: true
                });
                element.removeEventListener("pointerdown", onpointerdown);
                element.removeEventListener("pointermove", onpointermove);
            };
        }
    };
}
