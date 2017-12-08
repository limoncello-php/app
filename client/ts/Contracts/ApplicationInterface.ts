import {TokenInterface} from './TokenInterface';
import {SettingsInterface} from './SettingsInterface';

/**
 * Main application interface.
 */
export interface ApplicationInterface {
    /**
     * Settings.
     */
    readonly settings: SettingsInterface;

    /**
     * If the application has authentication token to work with backend.
     */
    readonly hasAuthToken: boolean;

    /**
     * Authentication token.
     */
    readonly authToken: TokenInterface;

    /**
     * A user name for the last successful authentication.
     */
    readonly lastUserName: string | null;

    /**
     * User ID from token.
     */
    readonly userId: number | undefined;

    /**
     * User name from token.
     */
    readonly userName: string | undefined;

    /**
     * Request authentication token from backend.
     *
     * @param {string} userName
     * @param {string} password
     * @param {string} scope
     *
     * @returns {Promise<TokenInterface>}
     */
    requestAuthToken(userName: string, password: string, scope?: string): Promise<TokenInterface>;

    /**
     * Request authentication token refresh.
     *
     * @returns {Promise<TokenInterface>}
     */
    refreshAuthToken(): Promise<TokenInterface>;

    /**
     * Forget authentication token.
     *
     * @returns {ApplicationInterface}
     */
    forgetAuthToken(): ApplicationInterface;

    /**
     * Forget user name for last successful authentication.
     *
     * @returns {ApplicationInterface}
     */
    forgetLastUserName(): ApplicationInterface;

    /**
     * Check if user token has scope assigned.
     *
     * @param {string} scope
     *
     * @returns {boolean}
     */
    userHasScope(scope: string): boolean;

    /**
     * @param {string} subUrl
     *
     * @returns Promise with JSON API response.
     *
     * @link http://jsonapi.org/format/#document-structure
     */
    apiRead(subUrl: string): Promise<any>;

    /**
     * @param {string} subUrl
     * @param {string} body
     *
     * @returns Promise with JSON API response.
     *
     * @link http://jsonapi.org/format/#crud-creating
     */
    apiCreate(subUrl: string, body: string): Promise<any>;

    /**
     * @param {string} subUrl
     * @param {string} body
     *
     * @returns Promise with JSON API response.
     *
     * @link http://jsonapi.org/format/#crud-updating
     */
    apiUpdate(subUrl: string, body: string): Promise<any>;

    /**
     * @param {string} subUrl
     *
     * @returns Promise with JSON API response.
     *
     * @link http://jsonapi.org/format/#crud-deleting
     */
    apiDelete(subUrl: string): Promise<any>;
}
