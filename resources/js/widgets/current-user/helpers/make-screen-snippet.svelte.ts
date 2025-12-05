import { createRawSnippet, mount, unmount } from "svelte";

import type { LayoutProps } from "../model/types";
import type { Component } from "svelte";
import type { Except } from "type-fest";

export function makeScreenSnippet<P extends Record<string, unknown>>(
    component: Component<P>,
    props: Except<P, keyof LayoutProps>
) {
    return createRawSnippet<[LayoutProps]>((sharedProps) => ({
        render: () =>
            `<screen-wrapper style="display: contents"></screen-wrapper>`,
        setup: (target) => {
            const instance = mount(component, {
                target: target,
                props: {
                    ...sharedProps(),
                    ...props
                } as unknown as P
            });
            return () => unmount(instance);
        }
    }));
}
