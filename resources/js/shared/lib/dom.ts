export function raf(fn: VoidFunction) {
    const frame = requestAnimationFrame(fn);
    return () => cancelAnimationFrame(frame);
}
