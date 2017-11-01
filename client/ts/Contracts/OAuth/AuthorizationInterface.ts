import {TokenInterface} from './TokenInterface';

/**
 * OAuth 2.0 authorization.
 *
 * @link https://tools.ietf.org/html/rfc6749#section-4
 */
export interface AuthorizationInterface {
    /**
     * Resource Owner Password Credentials Grant.
     *
     * @link https://tools.ietf.org/html/rfc6749#section-4.3
     */
    password(userName: string, password: string, scope?: string): Promise<TokenInterface>;

    /**
     * Refreshing an Access Token.
     *
     * @link https://tools.ietf.org/html/rfc6749#section-6
     */
    refresh(refreshToken: string, scope?: string): Promise<TokenInterface>;
}
