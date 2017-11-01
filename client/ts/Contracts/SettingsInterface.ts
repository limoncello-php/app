/**
 * Application settings.
 */
export interface SettingsInterface {
    /**
     * Unique version of the application.
     */
    readonly version: string;
    /**
     * URL for OAuth 2 token endpoint (e.g. https://example.com/token).
     */
    readonly tokenUrl: string;
    /**
     * Fetch options to be used for working with token endpoint (extra headers, CORS/no CORS, cache, etc).
     *
     * Note: `body` property will be overwritten.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/API/WindowOrWorkerGlobalScope/fetch#Parameters
     */
    readonly tokenFetchOptions: RequestInit;
    /**
     * Base URL for API (e.g. https://example.com/api/v1).
     */
    readonly apiBaseUrl: string;
    /**
     * Fetch options to be used for working with API (extra headers, CORS/no CORS, cache, etc).
     *
     * Note: `method` property will be overwritten (recommended leave blank).
     * Note: `body` property might be overwritten (recommended leave blank).
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/API/WindowOrWorkerGlobalScope/fetch#Parameters
     */
    readonly apiFetchOptions: RequestInit;
    /**
     * A list of URLs to be cached on application installation.
     */
    readonly preCacheUrls: string[];
    /**
     * Fetch options to be used for working with Cache GET methods (extra headers, CORS/no CORS, etc).
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/API/WindowOrWorkerGlobalScope/fetch#Parameters
     */
    readonly cacheFetchOptions: RequestInit;
}
