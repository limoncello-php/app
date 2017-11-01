import {ErrorLinksInterface} from './ErrorLinksInterface';
import {QueryParameterInterface} from './QueryParameterInterface';
import {DocumentPointerInterface} from './DocumentPointerInterface';

/**
 * Error Object.
 *
 * @link http://jsonapi.org/format/#error-objects
 */
export interface ErrorObjectInterface {
    /**
     * Unique identifier for this particular occurrence of the problem.
     */
    readonly id?: string;
    /**
     * Error links object.
     */
    readonly links?: ErrorLinksInterface;
    /**
     * Application-specific error code.
     */
    readonly code?: string;
    /**
     * A short, human-readable summary of the problem that
     * SHOULD NOT change from occurrence to occurrence of the problem,
     * except for purposes of localization.
     */
    readonly title?: string;
    /**
     * Human-readable explanation specific to this occurrence of the problem.
     * This value can be localized.
     */
    readonly detail?: string;
    /**
     * Object containing references to the source of the error.
     */
    readonly source?: DocumentPointerInterface | QueryParameterInterface;
    /**
     * Non-standard meta-information about the error.
     */
    readonly meta: any;
}
