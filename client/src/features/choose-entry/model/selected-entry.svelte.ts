type SelectedEntry = {
    type: "todo" | "event";
    id: string;
};

let selected: SelectedEntry | null = $state(null);

export const SelectedEntry = {
    update(entry: SelectedEntry | null): void {
        selected = entry;
    },
    get current(): SelectedEntry | null {
        return selected;
    }
};
