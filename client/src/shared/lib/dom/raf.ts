export function raf(fn: VoidFunction): () => void {
    const frame = requestAnimationFrame(fn);
    return () => cancelAnimationFrame(frame);
}
