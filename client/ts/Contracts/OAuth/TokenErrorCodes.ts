/**
 * @link https://tools.ietf.org/html/rfc6749#section-5.2
 */
export type TokenErrorCodes =
    "invalid_request"
    | "invalid_client"
    | "invalid_grant"
    | "unauthorized_client"
    | "unsupported_grant_type"
    | "invalid_scope";
