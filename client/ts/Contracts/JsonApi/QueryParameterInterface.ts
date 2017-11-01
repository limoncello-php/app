/**
 * Query parameter.
 *
 * @link http://jsonapi.org/format/#error-objects
 */
export interface QueryParameterInterface {
    /**
     * URI query parameter caused the error.
     */
    readonly parameter: string;
}
