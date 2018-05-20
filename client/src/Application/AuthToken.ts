import {
    Authorizer,
    AuthorizerInterface,
    ClientRequestsInterface,
    TokenInterface
} from '@limoncello-framework/oauth-client'

/**
 * A wrapper for sending HTML forms to OAuth server.
 */
class ClientRequests implements ClientRequestsInterface {
    /**
     * Authorization server endpoint URL.
     */
    private readonly tokenUrl: string;

    /**
     * Request parameters.
     */
    private readonly requestInit: RequestInit;

    /**
     * @param {string} tokenUrl Authorization server endpoint URL.
     * @param {RequestInit} requestInit Request parameters.
     */
    constructor(tokenUrl: string, requestInit: RequestInit) {
        this.tokenUrl = tokenUrl;
        this.requestInit = requestInit;
    }

    /**
     * Send HTML form data.
     *
     * @param data Data to send.
     * @param {boolean} addAuth If OAuth client authentication should be added to the request.
     *
     * @returns {Promise<Response>}
     */
    sendForm(data: any, addAuth: boolean): Promise<Response> {
        // We use only password authentication and OAuth client authentication is not used in password authentication.
        // So it is a safety measure.
        if (addAuth === true) {
            throw new Error('OAuth client authentication is not supported.');
        }

        let init: RequestInit = Object.assign({}, this.requestInit);

        let form = new FormData();
        Object.getOwnPropertyNames(data).forEach((name) => form.append(name, data[name]));
        init.body = form;

        // Fetch API has pretty good support in modern browsers.
        // If you need to support older ones feel free to use other means to send forms.
        //
        // https://caniuse.com/#search=fetch
        return fetch(this.tokenUrl, init);
    }
}

/**
 * Authorizes by user credentials.
 */
export class AuthToken {
    /**
     * The actual authorizer that does OAuth heavy lifting.
     */
    private readonly authorizer: AuthorizerInterface;

    /**
     * @param {string} tokenUrl Authorization server endpoint URL.
     * @param {RequestInit} requestInit Optional request parameters.
     */
    constructor(
        tokenUrl: string,
        requestInit: RequestInit = {
            method: "post",
            mode: "cors",
            credentials: "omit",
            cache: "no-cache",
        }
    ) {
        this.authorizer = new Authorizer(new ClientRequests(tokenUrl, requestInit))
    }

    /**
     *
     * @param {string} userName Required user email.
     * @param {string} password Required user password.
     * @param {string} scope Optional OAuth scopes to assign to OAuth token.
     *                       If not given all users scopes will be assigned.
     *
     * @returns {Promise<TokenInterface>}
     */
    password(userName: string, password: string, scope?: string): Promise<TokenInterface> {
        return this.authorizer.password(userName, password, scope);
    }

    /**
     * Refresh OAuth token with a new one.
     *
     * @param {string} refreshToken Required refresh token value.
     * @param {string} scope Optional OAuth scopes to assign to OAuth token.
     *                       If not given all scopes assigned to the previous token will be included.
     *
     * @returns {Promise<TokenInterface>}
     */
    refresh(refreshToken: string, scope?: string): Promise<TokenInterface> {
        return this.authorizer.refresh(refreshToken, scope);
    }
}
