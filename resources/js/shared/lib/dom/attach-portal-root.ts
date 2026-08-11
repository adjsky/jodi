export function attachPortalRoot(container: HTMLElement): HTMLElement {
    const portalRoot = document.createElement("div");
    portalRoot.id = "portal-root";

    container.append(portalRoot);

    return portalRoot;
}
