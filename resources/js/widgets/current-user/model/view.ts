import { HistoryView } from "$/shared/integrations/inertia";

export const view = new HistoryView<{ isDelete?: boolean }>(null, {
    viewTransition: true
});

export function buildViewName(...name: string[]): string {
    return "me/" + name.join("/");
}
