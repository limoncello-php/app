import {ErrorInterface} from '../Contracts/JsonApi/ErrorInterface';

/**
 * JsonApi error.
 */
export class JsonApiError extends Error {
    /**
     * @internal
     */
    private readonly _status: number;

    /**
     * @internal
     */
    private readonly _reason?: ErrorInterface;

    /**
     * @param {number} httpStatus
     * @param {ErrorInterface} reason
     * @param args
     */
    public constructor(httpStatus: number, reason: ErrorInterface | undefined = undefined, ...args: any[]) {
        super(...args);

        this._status = httpStatus;
        this._reason = reason;
    }

    /**
     * Getter.
     */
    get reason(): ErrorInterface | undefined {
        return this._reason;
    }

    /**
     * Getter.
     */
    public get httpStatus(): number {
        return this._status;
    }
}
