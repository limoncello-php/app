/**
 * Application cache.
 */
export interface CacheInterface {
    /**
     * Cache response by URL or request.
     */
    add(request: RequestInfo): Promise<void>;

    /**
     * Cache responses by multiple URLs or requests.
     */
    addAll(requests: RequestInfo[]): Promise<void>;

    /**
     * Get response by URL or request.
     */
    get(request: RequestInfo): Promise<Response | undefined>;

    /**
     * Remove all caches other than the current (typically old ones).
     */
    clearPrevious(): Promise<void>;
}
