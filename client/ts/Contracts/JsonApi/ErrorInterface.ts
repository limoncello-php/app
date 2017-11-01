import {ErrorObjectInterface} from './ErrorObjectInterface';

/**
 * Error.
 *
 * @link http://jsonapi.org/format/#error-objects
 */
export interface ErrorInterface {
    /**
     * Error objects array.
     */
    readonly errors: ErrorObjectInterface[];
}
