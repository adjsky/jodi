export type Session = Pick<
    App.Domain.Identity.Data.Output.AuthSessionData,
    "user" | "pushSubscription"
> & {
    id: "current";
};
