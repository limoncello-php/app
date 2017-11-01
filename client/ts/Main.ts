import './../sass/main.scss';

import {Events} from './Dom/Events';
import {App} from './Application/App';
import {Authorization} from './OAuth/Authorization';
import {SettingsInterface} from './Contracts/SettingsInterface';

(() => {
    const tokenFetchOptions: RequestInit = {
        method: "post",
        mode: "cors",
        credentials: "omit",
        cache: "no-cache",
    };

    const apiFetchOptions: RequestInit = {
        method: "post",
        mode: "cors",
        credentials: "omit",
        cache: "no-cache",
    };

    const cacheFetchOptions: RequestInit = {
        mode: "no-cors",
        //credentials: "omit",
        //cache: "no-cache",
    };

    const settings: SettingsInterface = {
        version: "1",
        tokenUrl: "http://localhost:8080/token",
        tokenFetchOptions: tokenFetchOptions,
        apiBaseUrl: "http://localhost:8080/api/v1",
        apiFetchOptions: apiFetchOptions,
        preCacheUrls: [],
        cacheFetchOptions: cacheFetchOptions,
    };

    const auth = new Authorization(settings.tokenUrl, settings.tokenFetchOptions);
    const app = new App(settings, auth);

    new Events(app);
})();
