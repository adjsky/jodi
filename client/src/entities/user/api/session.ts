import { api } from "#shared/api/client.ts";
import GetCurrentSession from "#wayfinder/actions/App/Domain/Identity/Actions/GetCurrentSession.ts";

export function getCurrentSession(): Promise<App.Domain.Identity.Data.Output.AuthSessionData> {
    const { url, method } = GetCurrentSession();

    return api(url, { method }).json();
}
