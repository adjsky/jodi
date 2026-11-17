<script
    lang="ts"
    generics="InputSchema extends z.ZodType | undefined = undefined, DataSchema extends StandardSchemaV1 | undefined = undefined"
>
    import { m } from "#paraglide/messages.js";
    import { use_loading_debounce } from "#shared/lib/svelte/use-loading-debounce.svelte.ts";
    import { isHTTPError, isNetworkError } from "ky";
    import { isShallowEqual } from "remeda";
    import { onMount } from "svelte";
    import z from "zod";

    import { api } from "../api/client.ts";
    import { toaster } from "./toaster.ts";

    import type { MaybePromise } from "#shared/lib/async/types.ts";
    import type { RouteDefinition } from "#wayfinder/wayfinder/index.ts";
    import type { StandardSchemaV1, StandardSchemaV1InferOutput } from "ky";
    import type { Snippet } from "svelte";
    import type { SvelteHTMLElements } from "svelte/elements";
    import type { Except } from "type-fest";

    const ApiErrorValidationSchema = z.object({
        errors: z.record(z.string(), z.tuple([z.string()], z.string()))
    });

    type FormErrors = Record<string, [string, ...string[]] | undefined>;

    type Input = InputSchema extends z.ZodType ? z.infer<InputSchema> : Record<string, unknown>;

    type Data = DataSchema extends StandardSchemaV1
        ? StandardSchemaV1InferOutput<DataSchema>
        : unknown;

    type FormState = {
        errors: FormErrors;
        submitting: boolean;
        delayed: boolean;
        is_dirty: boolean;
    };

    type Props = Except<
        SvelteHTMLElements["form"],
        "action" | "children" | "method" | "onsubmit"
    > & {
        action: RouteDefinition<"post" | "put" | "patch" | "delete">;
        children?: Snippet<[FormState]>;
        input_schema?: InputSchema;
        data_schema?: DataSchema;
        disable_while_processing?: boolean;
        transform?: (input: Input) => MaybePromise<unknown>;
        on_before?: () => boolean | void;
        on_submit?: VoidFunction;
        on_validation_error?: (errors: FormErrors) => void;
        on_rate_limit?: (retry_after: number) => void;
        on_success?: (data: Data, input: Input) => MaybePromise<void>;
        on_http_error?: (status: number, message: string) => void;
        on_finish?: VoidFunction;
    };

    let {
        action,
        children,
        input_schema,
        data_schema,
        disable_while_processing = false,
        transform,
        on_before,
        on_submit,
        on_validation_error,
        on_rate_limit,
        on_success,
        on_http_error,
        on_finish,
        ...props
    }: Props = $props();

    let form: HTMLFormElement | null = $state(null);
    let default_data: Record<string, FormDataEntryValue> = {};

    let submitting = $state(false);
    let delayed = use_loading_debounce(() => submitting, 200);
    let errors = $state.raw<FormErrors>({});
    let is_dirty = $state(false);

    onMount(() => {
        default_data = get_form_data();

        const events = ["input", "change", "reset"];

        events.map((event) => form?.addEventListener(event, update_dirty_state));
    });

    function update_dirty_state(e: Event) {
        if (e.type === "reset") {
            is_dirty = false;
        } else {
            is_dirty = !isShallowEqual(default_data, get_form_data());
        }
    }

    function get_form_data(submitter?: HTMLElement | null) {
        if (!form) {
            throw new Error("Can't get form data with no form element set.");
        }

        return Object.fromEntries(new FormData(form, submitter).entries());
    }

    async function handle_submit(e: SubmitEvent): Promise<void> {
        e.preventDefault();

        if (submitting || on_before?.() === false) {
            return;
        }

        errors = {};

        let json: unknown = get_form_data(e.submitter);

        if (input_schema) {
            const result = z.safeParse(input_schema, json);

            if (!result.success) {
                errors = z.flattenError(result.error).fieldErrors;
                on_validation_error?.(errors);
                return;
            }

            json = result.data;
        }

        if (transform) {
            json = await transform(json as Input);
        }

        submitting = true;
        on_submit?.();

        try {
            const request = api(action.url, {
                json,
                method: action.method
            });
            const response = await request;

            let data;

            if (response.status !== 204) {
                if (data_schema) {
                    data = await request.json(data_schema);
                } else {
                    data = await request.json();
                }
            }

            await on_success?.(data as Data, json as Input);
        } catch (e) {
            if (isHTTPError(e)) {
                const { response } = e;

                switch (response.status) {
                    case 422: {
                        const result = z.safeParse(ApiErrorValidationSchema, e.data);

                        if (result.success) {
                            errors = result.data.errors;
                            on_validation_error?.(errors);
                        }

                        break;
                    }

                    // These are handled by api client, we don't need to do anyhing here.
                    case 401:
                    case 403: {
                        break;
                    }

                    case 429: {
                        const retry_after = Number(response.headers.get("retry-after"));

                        on_rate_limit?.(retry_after);

                        break;
                    }

                    default: {
                        const message =
                            typeof e.data === "object" &&
                            e.data !== null &&
                            "message" in e.data &&
                            typeof e.data.message === "string"
                                ? e.data.message
                                : m["common.errors.unexpected"]();

                        if (on_http_error) {
                            on_http_error(response.status, message);
                        } else {
                            toaster.error(message);
                        }
                    }
                }
            } else if (!isNetworkError(e)) {
                toaster.error(m["common.errors.unexpected"]());
            }
        } finally {
            submitting = false;
            on_finish?.();
        }
    }
</script>

<form
    bind:this={form}
    {...props}
    aria-busy={submitting}
    novalidate={Boolean(input_schema) || props.novalidate}
    inert={disable_while_processing && submitting ? true : props.inert}
    onsubmit={handle_submit}
>
    {@render children?.({
        submitting,
        errors,
        is_dirty,
        get delayed() {
            return delayed.current;
        }
    })}
</form>
