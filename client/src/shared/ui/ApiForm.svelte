<script
    lang="ts"
    generics="InputSchema extends z.ZodType | undefined = undefined, DataSchema extends StandardSchemaV1 | undefined = undefined"
>
    import { m } from "#paraglide/messages.js";
    import { useLoadingDebounce } from "#shared/lib/svelte/use-loading-debounce.svelte.ts";
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

    type Input = InputSchema extends z.ZodType
        ? z.infer<InputSchema>
        : Record<string, unknown>;

    type Data = DataSchema extends StandardSchemaV1
        ? StandardSchemaV1InferOutput<DataSchema>
        : unknown;

    type FormState = {
        errors: FormErrors;
        submitting: boolean;
        delayed: boolean;
        isDirty: boolean;
    };

    type Props = Except<
        SvelteHTMLElements["form"],
        "action" | "children" | "method" | "onsubmit"
    > & {
        action: RouteDefinition<"post" | "put" | "patch" | "delete">;
        children?: Snippet<[FormState]>;
        inputSchema?: InputSchema;
        dataSchema?: DataSchema;
        disableWhileProcessing?: boolean;
        transform?: (input: Input) => MaybePromise<unknown>;
        onBefore?: () => boolean | void;
        onSubmit?: VoidFunction;
        onValidationError?: (errors: FormErrors) => void;
        onRateLimit?: (retryAfter: number) => void;
        onSuccess?: (data: Data, input: Input) => MaybePromise<void>;
        onHttpError?: (status: number, message: string) => void;
        onFinish?: VoidFunction;
    };

    let {
        action,
        children,
        inputSchema,
        dataSchema,
        disableWhileProcessing = false,
        transform,
        onBefore,
        onSubmit,
        onValidationError,
        onRateLimit,
        onSuccess,
        onHttpError,
        onFinish,
        ...props
    }: Props = $props();

    let form: HTMLFormElement | null = $state(null);
    let defaultData: Record<string, FormDataEntryValue> = {};

    let submitting = $state(false);
    let delayed = useLoadingDebounce(() => submitting, 200);
    let errors = $state.raw<FormErrors>({});
    let isDirty = $state(false);

    onMount(() => {
        defaultData = getFormData();

        const events = ["input", "change", "reset"];

        events.map((event) => form?.addEventListener(event, updateDirtyState));
    });

    function updateDirtyState(e: Event) {
        if (e.type === "reset") {
            isDirty = false;
        } else {
            isDirty = !isShallowEqual(defaultData, getFormData());
        }
    }

    function getFormData(submitter?: HTMLElement | null) {
        if (!form) {
            throw new Error("Can't get form data with no form element set.");
        }

        return Object.fromEntries(new FormData(form, submitter).entries());
    }

    async function handleSubmit(e: SubmitEvent): Promise<void> {
        e.preventDefault();

        if (submitting || onBefore?.() === false) {
            return;
        }

        errors = {};

        let json: unknown = getFormData(e.submitter);

        if (inputSchema) {
            const result = z.safeParse(inputSchema, json);

            if (!result.success) {
                errors = z.flattenError(result.error).fieldErrors;
                onValidationError?.(errors);
                return;
            }

            json = result.data;
        }

        if (transform) {
            json = await transform(json as Input);
        }

        submitting = true;
        onSubmit?.();

        try {
            const request = api(action.url, {
                json,
                method: action.method
            });
            const response = await request;

            let data;

            if (response.status !== 204) {
                if (dataSchema) {
                    data = await request.json(dataSchema);
                } else {
                    data = await request.json();
                }
            }

            await onSuccess?.(data as Data, json as Input);
        } catch (e) {
            if (isHTTPError(e)) {
                const { response } = e;

                switch (response.status) {
                    case 422: {
                        const result = z.safeParse(
                            ApiErrorValidationSchema,
                            e.data
                        );

                        if (result.success) {
                            errors = result.data.errors;
                            onValidationError?.(errors);
                        }

                        break;
                    }

                    // These are handled by api client, we don't need to do anyhing here.
                    case 401:
                    case 403: {
                        break;
                    }

                    case 429: {
                        const retryAfter = Number(
                            response.headers.get("retry-after")
                        );

                        onRateLimit?.(retryAfter);

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

                        if (onHttpError) {
                            onHttpError(response.status, message);
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
            onFinish?.();
        }
    }
</script>

<form
    bind:this={form}
    {...props}
    aria-busy={submitting}
    novalidate={Boolean(inputSchema) || props.novalidate}
    inert={disableWhileProcessing && submitting ? true : props.inert}
    onsubmit={handleSubmit}
>
    {@render children?.({
        submitting,
        errors,
        isDirty,
        get delayed() {
            return delayed.current;
        }
    })}
</form>
