/**
 * OAuth 2.0 token description.
 *
 * @link https://tools.ietf.org/html/rfc6749#section-5.1
 */
export interface TokenInterface {
    /**
     * The access token issued by the authorization server.
     */
    readonly access_token: string;
    /**
     * The type of the token issued.
     */
    readonly token_type: string;
    /**
     * The lifetime in seconds of the access token.
     */
    readonly expires_in?: number;
    /**
     * The refresh token, which can be used to obtain new access tokens using the same authorization grant.
     */
    readonly refresh_token?: string;
    /**
     * The scope of the access token.
     */
    readonly scope?: string;
    /**
     * Date and time when the token expires.
     * This is a wrapper over expires_in created when the token received.
     */
    readonly expires_at?: Date;
}
