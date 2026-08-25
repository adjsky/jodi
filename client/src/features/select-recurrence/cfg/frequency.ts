import { RRule } from "rrule";

export enum SelectableFrequency {
    DAILY = "daily",
    WEEKLY = "weekly",
    MONTHLY = "monthly",
    YEARLY = "yearly",
    HOURLY = "hourly",
    MINUTELY = "minutely",
    SECONDLY = "secondly"
}

export const SELECTABLE_TO_RRULE_FREQUENCY_MAP = {
    [SelectableFrequency.SECONDLY]: RRule.SECONDLY,
    [SelectableFrequency.MINUTELY]: RRule.MINUTELY,
    [SelectableFrequency.HOURLY]: RRule.HOURLY,
    [SelectableFrequency.DAILY]: RRule.DAILY,
    [SelectableFrequency.WEEKLY]: RRule.WEEKLY,
    [SelectableFrequency.MONTHLY]: RRule.MONTHLY,
    [SelectableFrequency.YEARLY]: RRule.YEARLY
};

export const RRULE_TO_SELECTABLE_FREQUENCY_MAP = {
    [RRule.SECONDLY]: SelectableFrequency.SECONDLY,
    [RRule.MINUTELY]: SelectableFrequency.MINUTELY,
    [RRule.HOURLY]: SelectableFrequency.HOURLY,
    [RRule.DAILY]: SelectableFrequency.DAILY,
    [RRule.WEEKLY]: SelectableFrequency.WEEKLY,
    [RRule.MONTHLY]: SelectableFrequency.MONTHLY,
    [RRule.YEARLY]: SelectableFrequency.YEARLY
};
