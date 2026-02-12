import { PLATFORM } from "$/shared/cfg/constants";
import * as Firebase from "$/shared/lib/firebase";

if (PLATFORM == "web") {
    Firebase.create();
}
