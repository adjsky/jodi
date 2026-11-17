export function get_caret_bottom_offset(textarea: HTMLTextAreaElement): number {
    const div = document.createElement("div");
    const span = document.createElement("span");
    const style = getComputedStyle(textarea);

    const properties = [
        "boxSizing",
        "width",
        "borderTopWidth",
        "borderLeftWidth",
        "borderRightWidth",
        "paddingTop",
        "paddingRight",
        "paddingBottom",
        "paddingLeft",
        "fontStyle",
        "fontVariant",
        "fontWeight",
        "fontSize",
        "fontFamily",
        "lineHeight",
        "letterSpacing",
        "wordSpacing",
        "textIndent",
        "tabSize",
        "whiteSpace",
        "wordWrap",
        "wordBreak"
    ] as const;

    properties.forEach((p) => (div.style[p] = style[p]));

    div.style.position = "fixed";
    div.style.top = "0";
    div.style.left = "-9999px";
    div.style.visibility = "hidden";
    div.style.whiteSpace = "pre-wrap";
    div.style.overflowWrap = "break-word";

    div.textContent = textarea.value.substring(0, textarea.selectionStart);

    span.textContent = "\u200b";
    div.append(span);

    document.body.appendChild(div);

    const div_rect = div.getBoundingClientRect();
    const span_rect = span.getBoundingClientRect();

    const bottom_offset = span_rect.bottom - div_rect.top - textarea.scrollTop;

    document.body.removeChild(div);

    return bottom_offset;
}
