import {TokenErrorInterface} from '../Contracts/OAuth/TokenErrorInterface';

/**
 * OAuth token error.
 */
export class TokenError extends Error {
    /**
     * @internal
     */
    private readonly _reason: TokenErrorInterface;

    /**
     * @param {TokenErrorInterface} reason
     * @param args
     */
    public constructor(reason: TokenErrorInterface, ...args: any[]) {
        super(...args);

        this._reason = reason;
        if (reason.error_description !== null && reason.error_description !== undefined) {
            this.message = reason.error_description;
        }
    }

    /**
     * Getter.
     *
     * @returns {TokenErrorInterface}
     */
    get reason(): TokenErrorInterface {
        return this._reason;
    }
}
