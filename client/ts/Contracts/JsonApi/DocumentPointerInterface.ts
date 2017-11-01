/**
 * Document pointer.
 *
 * @link http://jsonapi.org/format/#error-objects
 */
export interface DocumentPointerInterface {
    /**
     * JSON Pointer (RFC6901) to the associated entity in the request document.
     */
    readonly pointer: string;
}
