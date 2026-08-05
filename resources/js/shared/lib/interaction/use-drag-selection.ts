import { extract } from "runed";

import type { MaybeGetter } from "runed";
import type { Attachment } from "svelte/attachments";

const SELECTION_DELAY = 350;
const MOVE_TOLERANCE = 10;

type Options<T> = {
    enabled: boolean;
    isSelectable?: (value: T) => boolean;
    onStart?: VoidFunction;
    onSelect?: (value: T, anchor: T, phase: "start" | "move") => void;
    onEnd?: VoidFunction;
};

type DragSelection<T> = {
    attachment: (value: T) => Attachment<HTMLElement>;
};

export function useDragSelection<T>(
    options: MaybeGetter<Options<T>>
): DragSelection<T> {
    const values = new WeakMap<Element, T>();

    let timer: NodeJS.Timeout | null = null;
    let anchorElement: HTMLElement | null = null;
    let anchorValue: T | null = null;
    let currentValue: T | null = null;
    let pointerId: number | null = null;
    let isSelecting = false;
    let suppressClick = false;
    let startPosition = [0, 0];

    return {
        attachment: (value) => (element) => {
            values.set(element, value);

            function onpointerdown(e: PointerEvent) {
                const { enabled, isSelectable, onStart, onSelect } =
                    extract(options);

                if (
                    e.pointerType != "touch" ||
                    !enabled ||
                    isSelectable?.(value) === false ||
                    !e.isPrimary ||
                    pointerId != null
                ) {
                    return;
                }

                window.addEventListener("pointerup", onpointerend);
                window.addEventListener("pointercancel", onpointerend);
                window.addEventListener("touchmove", ontouchmove, {
                    passive: false
                });

                anchorElement = element;
                anchorValue = value;
                currentValue = value;
                pointerId = e.pointerId;
                startPosition = [e.clientX, e.clientY];

                timer = setTimeout(() => {
                    if (anchorValue == null) return;

                    timer = null;
                    isSelecting = true;
                    suppressClick = true;

                    onStart?.();
                    onSelect?.(value, anchorValue, "start");
                }, SELECTION_DELAY);
            }

            function onpointermove(e: PointerEvent) {
                const { isSelectable, onSelect } = extract(options);

                if (e.pointerId != pointerId) return;

                if (!isSelecting) {
                    const [x, y] = startPosition;
                    const distance = Math.hypot(e.clientX - x, e.clientY - y);
                    if (distance > MOVE_TOLERANCE) reset();
                    return;
                }

                if (anchorValue == null) return;

                let element = document.elementFromPoint(e.clientX, e.clientY);

                while (element) {
                    const value = values.get(element);
                    if (value !== undefined) {
                        if (
                            value !== currentValue &&
                            isSelectable?.(value) !== false
                        ) {
                            currentValue = value;
                            onSelect?.(value, anchorValue, "move");
                        }
                        break;
                    }
                    element = element.parentElement;
                }
            }

            function onpointerend(e: PointerEvent) {
                const { onEnd } = extract(options);

                if (e.pointerId != pointerId) return;

                const wasSelecting = isSelecting;
                const wasCancelled = e.type == "pointercancel";

                reset();

                if (wasSelecting) {
                    onEnd?.();
                }

                if (wasCancelled) {
                    suppressClick = false;
                } else if (wasSelecting) {
                    setTimeout(() => (suppressClick = false), 0);
                }
            }

            function onclick(e: PointerEvent) {
                if (!suppressClick) return;

                suppressClick = false;

                e.preventDefault();
                e.stopImmediatePropagation();
            }

            function ontouchmove(e: TouchEvent) {
                if (isSelecting && e.cancelable) {
                    e.preventDefault();
                }
            }

            function reset() {
                window.removeEventListener("pointerup", onpointerend);
                window.removeEventListener("pointercancel", onpointerend);
                window.removeEventListener("touchmove", ontouchmove);

                if (timer != null) {
                    clearTimeout(timer);
                    timer = null;
                }

                anchorElement = null;
                anchorValue = null;
                currentValue = null;
                pointerId = null;
                isSelecting = false;
                startPosition = [0, 0];
            }

            element.addEventListener("click", onclick, { capture: true });
            element.addEventListener("pointerdown", onpointerdown);
            element.addEventListener("pointermove", onpointermove);

            return () => {
                if (anchorElement == element) {
                    if (isSelecting) extract(options).onEnd?.();
                    suppressClick = false;
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
