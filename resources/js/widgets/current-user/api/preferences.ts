import { optimistic } from "$/shared/integrations/inertia";

import type { AppPageProps } from "$/globals";

export const preferences = (
    preferences: Partial<AppPageProps["auth"]["user"]["preferences"]>,
    error: string
) =>
    optimistic(
        (prev) => ({
            auth: {
                ...prev.auth,
                user: {
                    ...prev.auth.user,
                    preferences: {
                        ...prev.auth.user.preferences,
                        ...preferences
                    }
                }
            }
        }),
        { error }
    );
