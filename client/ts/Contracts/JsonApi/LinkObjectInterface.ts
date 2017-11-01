/**
 * Link Object.
 *
 * @link http://jsonapi.org/format/#document-links
 */
export interface LinkObjectInterface {
    /**
     * Link URL.
     */
    readonly href?: string;
    /**
     * Meta object containing non-standard information about the link.
     */
    readonly meta?: any;
}
