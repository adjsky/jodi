import { PLATFORM } from "$/shared/cfg/constants";
import * as Firebase from "$/shared/lib/firebase";
import * as PushSubscription from "$/shared/lib/push-subscription.svelte";

if (PLATFORM == "web") {
    Firebase.create();
}

void PushSubscription.synchronize();
