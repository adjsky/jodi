import { Context } from "runed";

import type { FriendData, RegistrationInvitationData } from "$/entities/user";
import type { ResourceReturn } from "runed";

export const context = new Context<{
    friends: ResourceReturn<FriendData[]>;
    invitations: ResourceReturn<RegistrationInvitationData[]>;
}>("widgets:current-user");
