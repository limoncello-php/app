import {JsonApiError} from '../JsonApi/JsonApiError';
import {TokenInterface} from '../Contracts/TokenInterface';
import {SettingsInterface} from '../Contracts/SettingsInterface';
import {ApplicationInterface} from '../Contracts/ApplicationInterface';
import {AuthorizerInterface} from '@limoncello-framework/oauth-client';

/**
 * A key for auth token in local storage.
 *
 * @type {string}
 */
const LS_KEY_AUTH_TOKEN: string = "token";

/**
 * A key for auth token in local storage.
 *
 * @type {string}
 */
const LS_KEY_USERNAME_FOR_LAST_SUCCESFUL_AUTH: string = "username";

/**
 * Error message.
 *
 * @type {string}
 */
const ERR_NO_AUTH_TOKEN: string = "Application has no valid authentication token.";

/**
 * Error message.
 *
 * @type {string}
 */
const ERR_NO_REFRESH_TOKEN: string = "Application has no valid refresh token.";

/**
 * Error message.
 *
 * @type {string}
 */
const ERR_API_CALL_FAILED: string = "API call failed.";

/**
 * Error message.
 *
 * @type {string}
 */
const INVALID_API_RESPONSE: string = "Invalid response from API.";

/**
 * Tuple of 2 strings for HTTP header key-value representation.
 */
type StringPair = [string, string];

/**
 * @inheritdoc
 */
export class App implements ApplicationInterface {
    /**
     * @see ApplicationInterface.settings
     *
     * @internal
     */
    private readonly _settings: SettingsInterface;

    /**
     *  @see ApplicationInterface.getAuthToken
     *
     * @internal
     */
    private _authToken?: TokenInterface;

    /**
     * @internal
     */
    private readonly _auth: AuthorizerInterface;

    /**
     * @param {SettingsInterface} settings
     * @param {AuthorizerInterface} authorization
     */
    public constructor(settings: SettingsInterface, authorization: AuthorizerInterface) {
        this._settings = settings;
        this._auth = authorization;

        this.setAuthToken(App.readAuthTokenFromLocalStorage());
        // remove the token if it is expired
        if (this.hasAuthToken === false) {
            this.setAuthToken(undefined);
        }
    }

    /**
     * @inheritdoc
     */
    public get settings(): SettingsInterface {
        return this._settings;
    }

    /**
     * @inheritdoc
     */
    public get hasAuthToken(): boolean {
        const noValidToken =
            this._authToken === undefined ||
            (<Date>this._authToken.expires_at).getTime() < Date.now();

        return !noValidToken;
    }

    /**
     * @inheritdoc
     */
    public get authToken(): TokenInterface {
        if (this.hasAuthToken === false) {
            throw new Error(ERR_NO_AUTH_TOKEN);
        }

        return <TokenInterface>this._authToken;
    }

    /**
     * @inheritdoc
     */
    public get lastUserName(): string | null {
        return localStorage.getItem(LS_KEY_USERNAME_FOR_LAST_SUCCESFUL_AUTH);
    }

    /**
     * @inheritdoc
     */
    public userHasScope(scope: string): boolean {
        let scopes;
        if (this.hasAuthToken && (scopes = this.authToken.scope) !== undefined) {
            for (const curScope of scopes.split(' ')) {
                if (scope === curScope) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public get userId(): number | undefined {
        const value = this.getTokenValue('id_user');

        let userId = undefined;
        if (typeof value === 'string') {
            userId = parseInt(value);
        } else if (value instanceof String) {
            userId = parseInt(value.toString());
        } else if (Number.isInteger(value)) {
            userId = value;
        }

        return userId;
    }

    /**
     * @inheritdoc
     */
    public get userName(): string | undefined {
        const firstName = this.getTokenValue('first_name');
        const lastName  = this.getTokenValue('last_name');

        return firstName && lastName ? firstName + ' ' + lastName : undefined;
    }

    /**
     * @inheritdoc
     */
    public requestAuthToken(userName: string, password: string, scope?: string): Promise<TokenInterface> {
        return this.auth
            .password(userName, password, scope)
            .then(token => {
                this.setAuthToken(token);
                this.setLastUserName(userName);

                return Promise.resolve(token);
            });
    }

    /**
     * @inheritdoc
     */
    public refreshAuthToken(): Promise<TokenInterface> {
        return Promise.resolve(this.authToken)
            .then(token => {
                if (token.refresh_token === undefined) {
                    throw new Error(ERR_NO_REFRESH_TOKEN);
                }

                return this.auth.refresh(<string>token.refresh_token);
            })
            .then(token => {
                this.setAuthToken(token);

                return Promise.resolve(token);
            })
            .catch(error => {
                this.forgetAuthToken();
                this.forgetLastUserName();

                throw error;
            });
    }

    /**
     * @inheritdoc
     */
    public forgetAuthToken(): ApplicationInterface {
        return this.setAuthToken(undefined);
    }

    /**
     * @inheritdoc
     */
    public forgetLastUserName(): ApplicationInterface {
        return this.setLastUserName(undefined);
    }

    /**
     * @inheritdoc
     */
    public apiRead(subUrl: string): Promise<any> {
        return this.fetchApi(
            'GET',
            [App.getJsonApiAcceptHeader()],
            subUrl
        );
    }

    /**
     * @inheritdoc
     */
    public apiCreate(subUrl: string, body: string): Promise<any> {
        return this.fetchApi(
            'POST',
            [App.getJsonApiAcceptHeader(), App.getJsonApiContentTypeHeader()],
            subUrl,
            body
        );
    }

    /**
     * @inheritdoc
     */
    public apiUpdate(subUrl: string, body: string): Promise<any> {
        return this.fetchApi(
            'PATCH',
            [App.getJsonApiAcceptHeader(), App.getJsonApiContentTypeHeader()],
            subUrl,
            body
        );
    }

    /**
     * @inheritdoc
     */
    public apiDelete(subUrl: string): Promise<any> {
        return this.fetchApi(
            'DELETE',
            [App.getJsonApiAcceptHeader()],
            subUrl
        );
    }

    /**
     * @param {string} name
     *
     * @returns {}
     */
    private getTokenValue(name: string): any | undefined {
        if (this.hasAuthToken) {
            const token = this.authToken;
            if (token.hasOwnProperty(name)) {
                return (<any>token)[name];
            }
        }

        return undefined;
    }

    /**
     * @param {string} method
     * @param {StringPair[]} headers
     * @param {string} subUrl
     * @param {string} body
     *
     * @returns {Promise}
     *
     * @internal
     */
    private fetchApi(method: string,
                     headers: StringPair[],
                     subUrl: string,
                     body: string | undefined = undefined): Promise<any> {
        // prepare API URL to call
        const url = this.settings.apiBaseUrl + subUrl;

        // prepare fetch options (clone default and apply inputs)
        let fetchOptions: RequestInit = Object.assign({}, this.settings.apiFetchOptions);
        fetchOptions.method = method;
        if (body !== undefined) {
            fetchOptions.body = body;
        }
        if (('headers' in fetchOptions) === false) {
            fetchOptions.headers = new Headers();
        }
        for (let pair of headers) {
            const [headerName, headerValue] = pair;
            (fetchOptions.headers as Headers).append(headerName, headerValue);
        }

        // if got valid token add it
        if (this.hasAuthToken === true) {
            (fetchOptions.headers as Headers).append("Authorizer", "Bearer " + this.authToken.access_token);
        }

        return fetch(url, fetchOptions)
            .then(response => {
                return response.json()
                    .then(json => {
                        // got json content

                        // extra check for content-type header
                        const headers = response.headers;
                        const hName = "content-type";
                        const hValue = "application/vnd.api+json";
                        if (headers.has(hName) === false ||
                            (headers.get(hName) as string).toLowerCase().includes(hValue) === false) {
                            throw new TypeError(INVALID_API_RESPONSE);
                        }

                        // is this JSON with actual data or JSON API error?
                        if (response.ok === false) {
                            // looks like it's JSON API error
                            throw new JsonApiError(
                                response.status,
                                json,
                                ERR_API_CALL_FAILED
                            );
                        }

                        // looks it's actual JSON API data
                        return Promise.resolve(json);
                    })
                    .catch(error => {
                        if (error instanceof TypeError || error instanceof JsonApiError) {
                            // rethrow the error from .then block above
                            throw error;
                        }

                        // no content / bad json

                        if (response.ok === true) {
                            // response was OK and it just don't have any data
                            return Promise.resolve(undefined);
                        } else {
                            // it was some error but we have only HTTP status code
                            throw new JsonApiError(
                                response.status,
                                undefined,
                                ERR_API_CALL_FAILED
                            );
                        }
                    });
            });
    }

    /**
     * @internal
     */
    private setAuthToken(token?: TokenInterface): ApplicationInterface {
        this._authToken = token;

        if (token) {
            localStorage.setItem(LS_KEY_AUTH_TOKEN, JSON.stringify(token));
        } else {
            localStorage.removeItem(LS_KEY_AUTH_TOKEN);
        }

        return this;
    }

    /**
     * @internal
     */
    private setLastUserName(userName?: string): ApplicationInterface {
        if (userName) {
            localStorage.setItem(LS_KEY_USERNAME_FOR_LAST_SUCCESFUL_AUTH, userName);
        } else {
            localStorage.removeItem(LS_KEY_USERNAME_FOR_LAST_SUCCESFUL_AUTH);
        }

        return this;
    }

    /**
     * @returns {TokenInterface}
     *
     * @internal
     */
    private static readAuthTokenFromLocalStorage(): TokenInterface | undefined {
        const tokenValue = localStorage.getItem(LS_KEY_AUTH_TOKEN);
        if (tokenValue === null) {
            return undefined;
        }

        // expires_at will be string instead of Date
        let almostToken = JSON.parse(tokenValue);
        almostToken.expires_at = new Date(almostToken.expires_at);

        // now it's a valid token

        return almostToken;
    }

    /**
     * Getter.
     *
     * @internal
     */
    private get auth(): AuthorizerInterface {
        return this._auth;
    }

    /**
     * Header name-value pair.
     *
     * @internal
     */
    private static getJsonApiContentTypeHeader(): StringPair {
        return ["Content-Type", "application/vnd.api+json"];
    }

    /**
     * Header name-value pair.
     *
     * @internal
     */
    private static getJsonApiAcceptHeader(): StringPair {
        return ["Accept", "application/vnd.api+json"];
    }
}
