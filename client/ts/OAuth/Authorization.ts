import {TokenInterface} from '../Contracts/OAuth/TokenInterface';
import {AuthorizationInterface} from '../Contracts/OAuth/AuthorizationInterface';
import {TokenError} from './TokenError';

/**
 * Error message.
 *
 * @type {string}
 */
const INVALID_TOKEN_RESPONSE: string = "Invalid token Response.";

/**
 * @inheritdoc
 */
export class Authorization implements AuthorizationInterface {
    /**
     * Token endpoint.
     *
     * @internal
     */
    private readonly tokenUrl: string;

    /**
     * Options for fetching token.
     *
     * @internal
     */
    private readonly fetchOptions: RequestInit;

    /**
     * Constructor.
     */
    public constructor(tokenUrl: string, fetchOptions: RequestInit) {
        this.tokenUrl = tokenUrl;
        this.fetchOptions = fetchOptions;
    }

    /**
     * @inheritdoc
     */
    public password(userName: string, password: string, scope?: string): Promise<TokenInterface> {
        let form = new FormData();
        form.append('grant_type', 'password');
        form.append('username', userName);
        form.append('password', password);
        if (scope !== undefined) {
            form.append('scope', <string>scope);
        }

        return this.fetchForm(form);
    }

    /**
     * @inheritdoc
     */
    public refresh(refreshToken: string, scope?: string): Promise<TokenInterface> {
        let form = new FormData();
        form.append('grant_type', 'refresh_token');
        form.append('refresh_token', refreshToken);
        if (scope !== undefined) {
            form.append('scope', <string>scope);
        }

        return this.fetchForm(form);
    }

    /**
     * Fetch form to token endpoint.
     *
     * @internal
     */
    private fetchForm(form: FormData): Promise<TokenInterface> {
        let fetchOptions = this.fetchOptions;
        fetchOptions.body = form;
        return fetch(this.tokenUrl, fetchOptions)
            .then(response => {
                return Promise.all([
                    response.json(),
                    Promise.resolve(response.ok),
                ])
            })
            .then(results => {
                const [json, isOk] = results;

                if (isOk === false) {
                    throw new TokenError(json);
                }

                // if expires_in was given then add expires_at
                if (json.expires_in !== undefined &&
                    Number.isInteger(json.expires_in) === true &&
                    json.expires_in > 0
                ) {
                    // let's have some time buffer for networking and etc.
                    const safetyBufferInSec = 5;
                    const expirationInSec = Math.max(json.expires_in - safetyBufferInSec, 0);
                    let expiresAt = new Date();
                    expiresAt.setTime(expiresAt.getTime() + expirationInSec * 1000);
                    json.expires_at = expiresAt;
                }

                return Promise.resolve(<TokenInterface>json);
            })
            .catch(error => {
                // rethrow the error from the block above
                if (error instanceof TokenError) {
                    throw error;
                }

                // if we are here the response was not JSON
                throw new TypeError(INVALID_TOKEN_RESPONSE);
            });
    }
}
