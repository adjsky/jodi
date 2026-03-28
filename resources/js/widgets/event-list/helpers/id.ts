export function id(event: App.Data.EventDto) {
    return event.id + "|" + (event.occursAt ?? event.startsAt);
}
