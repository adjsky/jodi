import { Action } from "./action.ts";
import { Subscription } from "./subscription.svelte";

export class PushService {
    subscription: Subscription;
    action: Action;

    constructor() {
        this.subscription = new Subscription();
        this.action = new Action();
    }
}
