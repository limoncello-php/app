import 'bootstrap';
import './../scss/index.scss';

export {AuthToken} from './Application/AuthToken';

// import {AuthToken} from './Application/AuthToken';
// import {QueryBuilder} from '@limoncello-framework/json-api-client';
// import {TokenInterface} from "@limoncello-framework/oauth-client";
//
// (async () => {
//     const serverUrl = 'http://localhost:8080';
//     const userName = 'denesik.stewart@gmail.com';
//     const password = 'secret';
//
//     const tokenUrl = serverUrl + '/token';
//     const apiPrefix = serverUrl + '/api/v1';
//
//     try {
//         const token: TokenInterface = await (new AuthToken(tokenUrl)).password(userName, password);
//
//         // see https://tools.ietf.org/html/rfc6749#section-5.1
//         console.log('token value ' + token.access_token);
//         console.log('token will expire in (seconds) ' + token.expires_in);
//         console.log('optional refresh token ' + token.refresh_token);
//
//         const apiUrl: string = apiPrefix + (new QueryBuilder('users'))
//             .withFilters({
//                 field: 'first-name',
//                 operation: 'like',
//                 parameters: '%a%'
//             }, {
//                 field: 'role',
//                 operation: 'equals',
//                 parameters: 'user'
//             })
//             .withSorts({
//                 field: 'last-name',
//                 isAscending: false
//             })
//             .withPagination(0, 10)
//             .index();
//
//         let requestParams: RequestInit = {
//             method: 'GET',
//             headers: {
//                 Authorization: 'Bearer ' + token.access_token,
//             }
//         };
//
//         const response = await fetch(apiUrl, requestParams);
//         const json = await response.json();
//
//         console.log(json.data);
//
//     } catch (error) {
//         if (error.reason !== undefined) {
//             // see https://tools.ietf.org/html/rfc6749#section-5.2
//             console.error('Authentication failed. Reason: ' + error.reason.error);
//         } else {
//             // invalid token URL, network error, invalid response format or server-side error
//             console.error('Error occurred: ' + error.message);
//         }
//     }
// })();
