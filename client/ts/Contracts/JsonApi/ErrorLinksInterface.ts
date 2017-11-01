import {LinkInterface} from './LinkInterface';

/**
 * Error Links.
 *
 * @link http://jsonapi.org/format/#error-objects
 */
export interface ErrorLinksInterface {
    /**
     * About Link.
     */
    readonly about: LinkInterface;
}
