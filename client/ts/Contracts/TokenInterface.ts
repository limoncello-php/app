import {TokenInterface as BaseTokenInterface} from '@limoncello-framework/oauth-client';

/**
 * @inheritDoc
 */
export interface TokenInterface extends BaseTokenInterface{
    /**
     * Date and time when the token expires.
     * This is a wrapper over expires_in created when the token received.
     */
    readonly expires_at?: Date;
}
