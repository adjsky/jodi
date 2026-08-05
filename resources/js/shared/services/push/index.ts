import { handleAction } from "./handle-action";
import { Subscription } from "./subscription.svelte";

const subscription = new Subscription();

export const Push = {
    handleAction,
    subscription
};
