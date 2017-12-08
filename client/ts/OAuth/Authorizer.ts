import {
    Authorizer as BaseAuthorizer,
    ClientRequestsInterface,
    TokenInterface as BaseTokenInterface
} from '@limoncello-framework/oauth-client';
import {TokenInterface} from '../Contracts/TokenInterface';

/**
 * @inheritdoc
 */
export class Authorizer extends BaseAuthorizer {
    /**
     * Constructor.
     */
    public constructor(tokenUrl: string, fetchOptions: RequestInit) {
        const clientRequests:ClientRequestsInterface  = {
            sendForm(data: any, addAuth: boolean) {
                if (addAuth === true) {
                    // Unlike server's side the code on user's side cannot have such auth info for security reasons.
                    throw new Error('Client authentication is not supported.');
                }

                // fill it a form. For more see https://developer.mozilla.org/en-US/docs/Web/API/FormData/FormData
                let form = new FormData();
                Object.getOwnPropertyNames(data).forEach((name) => form.append(name, data[name]));

                // see https://developer.mozilla.org/en-US/docs/Web/API/WindowOrWorkerGlobalScope/fetch
                // for a full list of available options.
                let init: RequestInit = Object.assign(fetchOptions, {body: form});

                return fetch(tokenUrl, init);
            }
        };
        super(clientRequests);
    }

    /**
     * @inheritdoc
     */
    public password(userName: string, password: string, scope?: string): Promise<TokenInterface>
    {
        return super.password(userName, password, scope).then((baseToken: BaseTokenInterface) => {
            let expiresAt = new Date();

            // if expires_in was given then add expires_at
            if (baseToken.expires_in !== undefined &&
                Number.isInteger(baseToken.expires_in) === true &&
                baseToken.expires_in > 0
            ) {
                // let's have some time buffer for networking and etc.
                const safetyBufferInSec = 5;
                const expirationInSec = Math.max(baseToken.expires_in - safetyBufferInSec, 0);
                expiresAt.setTime(expiresAt.getTime() + expirationInSec * 1000);
            }

            const token: TokenInterface = Object.assign({expires_at: expiresAt}, baseToken);

            return Promise.resolve(token);
        });
    }
}
