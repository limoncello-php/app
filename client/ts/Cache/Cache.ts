import {CacheInterface} from '../Contracts/CacheInterface';

/**
 * @inheritdoc
 */
export class Cache implements CacheInterface {
    /**
     * Unique cache version/name.
     *
     * @internal
     */
    private readonly version: string;

    /**
     * Query/match options for underlying cache.
     *
     * @internal
     */
    private readonly queryOptions?: CacheQueryOptions;

    /**
     * @param version Unique cache version/name.
     * @param queryOptions Query/match options for underlying cache.
     */
    public constructor(version: string, queryOptions: CacheQueryOptions | undefined = undefined) {
        this.version = version;
        this.queryOptions = queryOptions;
    }

    /**
     * @inheritdoc
     */
    public add(request: RequestInfo): Promise<void> {
        return this.getCache().then(cache => cache.add(request));
    }

    /**
     * @inheritdoc
     */
    public addAll(requests: RequestInfo[]): Promise<void> {
        return this.getCache().then(cache => cache.addAll(requests));
    }

    /**
     * @inheritdoc
     */
    public get(request: RequestInfo): Promise<Response | undefined> {
        return this.getCache().then(cache => cache.match(request, this.queryOptions));
    }

    /**
     * @inheritdoc
     */
    public clearPrevious(): Promise<void> {
        // TypeScript thinks `caches.keys` returns `any` while MDN says it returns `Promise`.
        // In order to resolve this inconsistency we wrap it with `Promise.resolve`.
        //
        // https://developer.mozilla.org/en-US/docs/Web/API/CacheStorage/keys

        return Promise.resolve(caches.keys()).then(cacheNames => {
            const resultPromises = (cacheNames as string[]).map(name => {
                return name !== this.version ? caches.delete(name) : Promise.resolve(false);
            });

            // wait for all results, ignore them and return Promise.resolve(undefined)
            return Promise.all(resultPromises).then(() => undefined);
        });
    }

    /**
     * Return underlying cache.
     *
     * @internal
     */
    private getCache() {
        return window.caches.open(this.version);
    }
}
