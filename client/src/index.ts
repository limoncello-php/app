import 'bootstrap';
import './../scss/index.scss';

// import {AuthToken} from './Application/AuthToken';
// import {QueryBuilder} from '@limoncello-framework/json-api-client';
// import {TokenInterface} from "@limoncello-framework/oauth-client";
//
// (async () => {
//
//     const serverUrl = 'http://localhost:8080';
//     const userName = 'denesik.stewart@gmail.com';
//     const password = 'secret';
//
//     try {
//         // Get OAuth token (for details see https://tools.ietf.org/html/rfc6749#section-5.1)
//         const token: TokenInterface = await (new AuthToken(serverUrl + '/token')).password(userName, password);
//         console.log('OAuth token: ' + JSON.stringify(token, null, ' '));
//         const authHeader = {Authorization: 'Bearer ' + token.access_token};
//
//         // CREATE Post sample
//         let response = await fetch(serverUrl + '/api/v1/posts', {
//             method: 'POST', headers: authHeader, body: JSON.stringify({
//                 data: {
//                     type: 'posts',
//                     attributes: {
//                         title: 'New title 12345',
//                         text: 'Some text',
//                     }
//                 }
//             })
//         });
//         console.log('New post created at: ' + JSON.stringify(response.headers.get('Location')));
//
//         // UPDATE Post sample
//         await fetch(serverUrl + '/api/v1/posts', {
//             method: 'PATCH', headers: authHeader, body: JSON.stringify({
//                 data: {
//                     id: '101',
//                     type: 'posts',
//                     attributes: {
//                         title: 'Udpated title',
//                     }
//                 }
//             })
//         });
//
//         // SEARCH Posts sample
//         const apiUrl: string = serverUrl + '/api/v1' + (new QueryBuilder('posts'))
//             .withFilters({
//                 field: 'title',
//                 operation: 'like',
//                 parameters: '%12345%'
//             })
//             .withSorts({
//                 field: 'text',
//                 isAscending: false
//             })
//             .withPagination(0, 10)
//             .index();
//         response = await fetch(apiUrl, {method: 'GET', headers: authHeader});
//         console.log(await response.json());
//
//         // DELETE Post sample
//         await fetch(serverUrl + '/api/v1/posts/101', {method: 'DELETE', headers: authHeader});
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
