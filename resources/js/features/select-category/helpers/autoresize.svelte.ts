export const useAutoresize = () => {
    let input: HTMLInputElement | null = null;

    const canvas = document.createElement("canvas");
    const ctx = canvas.getContext("2d");

    function resize(node: HTMLInputElement, value?: string) {
        if (!ctx) throw new Error("no context???");

        const style = window.getComputedStyle(node);
        ctx.font = `${style.fontWeight} ${style.fontSize} ${style.fontFamily}`;

        const text = (value ?? node.value) || node.placeholder || "";
        const textWidth = ctx.measureText(text).width;

        const padding =
            parseFloat(style.paddingLeft) + parseFloat(style.paddingRight);

        node.style.width = `${textWidth + padding}px`;
    }

    function autoresize(node: HTMLInputElement) {
        input = node;
        resize(node);
    }

    return Object.assign(autoresize, {
        update(value: string) {
            if (!input) {
                throw new Error("Attach first, then update.");
            }

            resize(input, value);
        }
    });
};
